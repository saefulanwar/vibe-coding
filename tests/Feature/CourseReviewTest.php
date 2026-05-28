<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\Certificate;
use App\Models\CourseReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseReviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;
    protected Course $course;
    protected CourseBatch $batch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::create([
            'name' => 'Development',
            'slug' => 'development',
        ]);

        $this->course = Course::create([
            'category_id' => $this->category->id,
            'title' => 'Web Development Bootcamp',
            'slug' => 'web-development-bootcamp',
            'description' => 'Learn full stack web development from scratch.',
            'price' => 150000.00,
            'is_published' => true,
            'source' => 'local',
        ]);

        $this->batch = CourseBatch::create([
            'course_id' => $this->course->id,
            'name' => 'Angkatan 1 - Web Dev',
            'quota' => 50,
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(20),
            'registration_end_date' => now()->addDays(5),
        ]);
    }

    /**
     * UAT Scenario 1: Security Gating
     * Ensure student who has not completed the course (no certificate) cannot give a review.
     */
    public function test_student_without_certificate_cannot_submit_review(): void
    {
        $response = $this->actingAs($this->user)->post("/courses/{$this->course->id}/reviews", [
            'rating' => 5,
            'review_text' => 'Bagus sekali!',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseEmpty('course_reviews');
    }

    /**
     * UAT Scenario 2: Validation & Data Integrity
     * Ensure student with completed status can submit review, rating is validated,
     * and review_text is correctly sanitized from any XSS tags.
     */
    public function test_student_with_certificate_can_submit_sanitized_review(): void
    {
        // 1. Create a completed certificate for user
        Certificate::create([
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
            'student_name_snapshot' => $this->user->name,
            'course_title_snapshot' => $this->course->title,
            'completed_at' => now(),
            'status' => 'completed',
            'file_path' => 'certificates/fake.pdf',
        ]);

        // 2. Try invalid rating (< 1)
        $responseInvalid = $this->actingAs($this->user)->post("/courses/{$this->course->id}/reviews", [
            'rating' => 0,
            'review_text' => 'Buruk',
        ]);
        $responseInvalid->assertSessionHasErrors('rating');

        // 3. Submit valid rating and review text containing XSS script tags
        $responseValid = $this->actingAs($this->user)->post("/courses/{$this->course->id}/reviews", [
            'rating' => 4,
            'review_text' => '<script>alert("hack");</script>Ulasan premium dan aman dari XSS!',
        ]);

        $responseValid->assertStatus(302); // Redirect back
        
        // 4. Assert review is saved and perfectly sanitized (strip_tags)
        $this->assertDatabaseHas('course_reviews', [
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
            'rating' => 4,
            'review_text' => 'alert("hack");Ulasan premium dan aman dari XSS!',
            'status' => 'published',
        ]);
    }

    /**
     * UAT Scenario 3: Real-Time Aggregate Calculation
     * Ensure that when reviews are added/edited/deleted, the course's average rating
     * and reviews count recalculates correctly and instantaneously.
     */
    public function test_rating_aggregates_recalculate_instantly(): void
    {
        // Assert initial state is default (0, 0)
        $this->course->refresh();
        $this->assertEquals(0, $this->course->reviews_count);
        $this->assertEquals(0.00, $this->course->average_rating);

        // Add first review
        $review1 = CourseReview::create([
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
            'rating' => 5,
            'review_text' => 'Ulasan 1',
            'status' => 'published',
        ]);

        $this->course->refresh();
        $this->assertEquals(1, $this->course->reviews_count);
        $this->assertEquals(5.00, $this->course->average_rating);

        // Add second review by another user
        $user2 = User::factory()->create();
        $review2 = CourseReview::create([
            'user_id' => $user2->id,
            'course_id' => $this->course->id,
            'rating' => 3,
            'review_text' => 'Ulasan 2',
            'status' => 'published',
        ]);

        $this->course->refresh();
        $this->assertEquals(2, $this->course->reviews_count);
        $this->assertEquals(4.00, $this->course->average_rating); // (5 + 3) / 2 = 4.00

        // Soft delete a review and make sure it is excluded from recalculation
        $review2->delete();

        $this->course->refresh();
        $this->assertEquals(1, $this->course->reviews_count);
        $this->assertEquals(5.00, $this->course->average_rating);
    }
}
