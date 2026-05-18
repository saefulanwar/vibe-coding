<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseBatchTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::create([
            'name' => 'IT & Software',
            'slug' => 'it-software',
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
    }

    /**
     * Test if dashboard page renders successfully for an authenticated student.
     */
    public function test_dashboard_renders_successfully(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Katalog Kursus');
    }

    /**
     * Test that checkout fails when the registration end date of a batch has passed.
     */
    public function test_checkout_fails_if_registration_has_ended(): void
    {
        $batch = CourseBatch::create([
            'course_id' => $this->course->id,
            'name' => 'Angkatan 1 - Lampau',
            'quota' => 20,
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(30),
            'registration_end_date' => now()->subDay(), // Passed yesterday
        ]);

        $response = $this->actingAs($this->user)->from('/dashboard')->post('/checkout', [
            'course_batch_id' => $batch->id,
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error', 'Waktu pendaftaran angkatan ini telah berakhir.');
    }

    /**
     * Test that checkout fails when the batch quota is fully booked.
     */
    public function test_checkout_fails_if_quota_is_full(): void
    {
        $batch = CourseBatch::create([
            'course_id' => $this->course->id,
            'name' => 'Angkatan 2 - Penuh',
            'quota' => 0, // No seats available
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(30),
            'registration_end_date' => now()->addDays(2),
        ]);

        $response = $this->actingAs($this->user)->from('/dashboard')->post('/checkout', [
            'course_batch_id' => $batch->id,
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error', 'Kuota angkatan ini sudah penuh.');
    }

    /**
     * Test that checkout redirects to the payment page when quota and registration dates are valid.
     */
    public function test_checkout_redirects_to_payment_if_valid(): void
    {
        $batch = CourseBatch::create([
            'course_id' => $this->course->id,
            'name' => 'Angkatan 3 - Terbuka',
            'quota' => 15,
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(30),
            'registration_end_date' => now()->addDays(3),
        ]);

        $response = $this->actingAs($this->user)->post('/checkout', [
            'course_batch_id' => $batch->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'course_batch_id' => $batch->id,
            'status' => 'pending',
        ]);
    }

    /**
     * Test that access to course lessons is locked before the batch start_date.
     */
    public function test_access_locked_before_start_date(): void
    {
        $batch = CourseBatch::create([
            'course_id' => $this->course->id,
            'name' => 'Angkatan 4 - Belum Mulai',
            'quota' => 10,
            'start_date' => now()->addDays(5), // Starts in 5 days
            'end_date' => now()->addDays(30),
            'registration_end_date' => now()->addDays(2),
        ]);

        // Enrolled user
        Enrollment::create([
            'user_id' => $this->user->id,
            'course_batch_id' => $batch->id,
            'enrolled_at' => now(),
        ]);

        // Trying to access course learn trigger
        $response = $this->actingAs($this->user)->post("/courses/{$this->course->id}/learn");

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
        $this->assertTrue(str_contains(session('error'), 'Kelas belum dimulai'));
    }
}
