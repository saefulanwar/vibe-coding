<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Unit;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseReview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CourseReviewAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminFakultas1;
    protected User $adminFakultas2;
    protected User $member;
    protected CourseReview $review;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $adminFakultasRole = Role::firstOrCreate(['name' => 'admin_fakultas']);
        $memberRole = Role::firstOrCreate(['name' => 'member']);

        // Create Units
        $unit1 = Unit::create(['code' => 'FIPP', 'name' => 'Fakultas Ilmu Pendidikan']);
        $unit2 = Unit::create(['code' => 'DPPK', 'name' => 'Direktorat']);

        // Create Users
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole($superAdminRole);

        $this->adminFakultas1 = User::factory()->create(['unit_id' => $unit1->id]);
        $this->adminFakultas1->assignRole($adminFakultasRole);

        $this->adminFakultas2 = User::factory()->create(['unit_id' => $unit2->id]);
        $this->adminFakultas2->assignRole($adminFakultasRole);

        $this->member = User::factory()->create();
        $this->member->assignRole($memberRole);

        // Create Course and Review for Unit 1
        $category = Category::create(['name' => 'TI', 'slug' => 'ti']);
        $course1 = Course::create([
            'category_id' => $category->id,
            'title' => 'Course Unit 1',
            'slug' => 'course-unit-1',
            'description' => 'Desc',
            'price' => 1000,
            'is_published' => true,
            'source' => 'local',
            'unit_id' => $unit1->id,
        ]);

        $this->review = CourseReview::create([
            'user_id' => $this->member->id,
            'course_id' => $course1->id,
            'rating' => 5,
            'review_text' => 'Bagus!',
            'status' => 'published',
        ]);
    }

    public function test_member_cannot_access_course_reviews_page(): void
    {
        $response = $this->actingAs($this->member)->get('/admin/course-reviews');
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_course_reviews_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/course-reviews');
        $response->assertStatus(200);
    }

    public function test_admin_fakultas_can_access_course_reviews_page(): void
    {
        $response = $this->actingAs($this->adminFakultas1)->get('/admin/course-reviews');
        $response->assertStatus(200);
    }

    public function test_admin_fakultas_can_only_access_their_own_unit_reviews_in_query(): void
    {
        // For adminFakultas1 (Unit 1), they should see the review
        $response1 = $this->actingAs($this->adminFakultas1)->get('/admin/course-reviews');
        $response1->assertStatus(200);

        // For adminFakultas2 (Unit 2), they should NOT see Unit 1's reviews in the query list
        $response2 = $this->actingAs($this->adminFakultas2)->get('/admin/course-reviews');
        $response2->assertStatus(200);
        
        // Let's verify Edit Page access
        $editUrl = "/admin/course-reviews/{$this->review->id}/edit";
        
        // adminFakultas1 has access to edit their course review
        $this->actingAs($this->adminFakultas1)->get($editUrl)->assertStatus(200);
        
        // adminFakultas2 does NOT have access to edit review of Unit 1 (returns 404 since it's filtered out of query)
        $this->actingAs($this->adminFakultas2)->get($editUrl)->assertStatus(404);
    }
}
