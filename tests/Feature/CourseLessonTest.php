<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\Enrollment;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseLessonTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;
    protected Course $course;
    protected CourseBatch $batch;
    protected Module $module;
    protected Lesson $lesson1;
    protected Lesson $lesson2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::create([
            'name' => 'Design & Multimedia',
            'slug' => 'design-multimedia',
        ]);

        $this->course = Course::create([
            'category_id' => $this->category->id,
            'title' => 'UI/UX Design Masterclass',
            'slug' => 'ui-ux-design-masterclass',
            'description' => 'Master Figma and modern UI/UX design rules.',
            'price' => 125000.00,
            'is_published' => true,
            'source' => 'local',
        ]);

        $this->batch = CourseBatch::create([
            'course_id' => $this->course->id,
            'name' => 'Angkatan 1 - Figma',
            'quota' => 50,
            'start_date' => now()->subDays(2), // Already started!
            'end_date' => now()->addDays(20),
            'registration_end_date' => now()->addDays(5),
        ]);

        $this->module = Module::create([
            'course_id' => $this->course->id,
            'title' => 'Pengenalan Figma',
            'sort_order' => 1,
        ]);

        $this->lesson1 = Lesson::create([
            'module_id' => $this->module->id,
            'title' => 'Dasar Frame & Grouping',
            'content_text' => 'Materi tentang Frame dan Group di Figma.',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'sort_order' => 1,
        ]);

        $this->lesson2 = Lesson::create([
            'module_id' => $this->module->id,
            'title' => 'Dasar Autolayout',
            'content_text' => 'Materi tentang Autolayout reaktif di Figma.',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'sort_order' => 2,
        ]);
    }

    /**
     * Test that student without enrollment cannot view lessons.
     */
    public function test_student_without_enrollment_cannot_view_lessons(): void
    {
        $response = $this->actingAs($this->user)->get("/courses/{$this->course->id}/lessons/{$this->lesson1->id}");

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke materi ini.');
    }

    /**
     * Test that student with active enrollment can view lessons.
     */
    public function test_student_with_enrollment_can_view_lessons(): void
    {
        Enrollment::create([
            'user_id' => $this->user->id,
            'course_batch_id' => $this->batch->id,
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get("/courses/{$this->course->id}/lessons/{$this->lesson1->id}");

        $response->assertStatus(200);
        $response->assertSee('Dasar Frame &amp; Grouping', false);
        $response->assertSee('Materi tentang Frame dan Group di Figma.');
    }

    /**
     * Test navigation between previous and next lessons.
     */
    public function test_lesson_navigation_links(): void
    {
        Enrollment::create([
            'user_id' => $this->user->id,
            'course_batch_id' => $this->batch->id,
            'enrolled_at' => now(),
        ]);

        // Access Lesson 1
        $response1 = $this->actingAs($this->user)->get("/courses/{$this->course->id}/lessons/{$this->lesson1->id}");
        $response1->assertStatus(200);
        $response1->assertSee('Dasar Autolayout'); // Next button or link should be visible

        // Access Lesson 2
        $response2 = $this->actingAs($this->user)->get("/courses/{$this->course->id}/lessons/{$this->lesson2->id}");
        $response2->assertStatus(200);
        $response2->assertSee('Dasar Frame &amp; Grouping', false); // Prev button or link should be visible
    }
}
