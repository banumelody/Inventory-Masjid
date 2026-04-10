<?php

namespace Tests\Feature;

use App\Models\Masjid;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private Masjid $masjid;
    private User $admin;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->masjid = Masjid::create([
            'name' => 'Masjid Test',
            'slug' => 'masjid-test',
            'address' => 'Jl. Test',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
        ]);

        $adminRole = Role::where('name', 'admin')->first();

        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.test',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'masjid_id' => $this->masjid->id,
        ]);

        $operatorRole = Role::where('name', 'operator')->first();
        $this->otherUser = User::create([
            'name' => 'Operator',
            'email' => 'operator@test.test',
            'password' => bcrypt('password'),
            'role_id' => $operatorRole->id,
            'masjid_id' => $this->masjid->id,
        ]);
    }

    public function test_notification_page_loads(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['current_masjid_id' => $this->masjid->id])
            ->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Notifikasi');
    }

    public function test_user_sees_own_notifications(): void
    {
        Notification::create([
            'user_id' => $this->admin->id,
            'masjid_id' => $this->masjid->id,
            'type' => 'loan_overdue',
            'title' => 'Peminjaman Jatuh Tempo',
            'message' => 'Test overdue notification',
        ]);

        Notification::create([
            'user_id' => $this->otherUser->id,
            'masjid_id' => $this->masjid->id,
            'type' => 'feedback_new',
            'title' => 'Feedback Baru',
            'message' => 'Test feedback notification',
        ]);

        $response = $this->actingAs($this->admin)
            ->withSession(['current_masjid_id' => $this->masjid->id])
            ->get(route('notifications.index'));

        $response->assertSee('Test overdue notification');
        $response->assertDontSee('Test feedback notification');
    }

    public function test_mark_notification_as_read(): void
    {
        $notification = Notification::create([
            'user_id' => $this->admin->id,
            'masjid_id' => $this->masjid->id,
            'type' => 'loan_overdue',
            'title' => 'Test',
            'message' => 'Test message',
        ]);

        $this->assertNull($notification->read_at);

        $response = $this->actingAs($this->admin)
            ->withSession(['current_masjid_id' => $this->masjid->id])
            ->post(route('notifications.markRead', $notification));

        $response->assertRedirect(route('notifications.index'));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_mark_notification_with_link_redirects(): void
    {
        $notification = Notification::create([
            'user_id' => $this->admin->id,
            'masjid_id' => $this->masjid->id,
            'type' => 'loan_overdue',
            'title' => 'Test',
            'message' => 'Test message',
            'link' => '/dashboard',
        ]);

        $response = $this->actingAs($this->admin)
            ->withSession(['current_masjid_id' => $this->masjid->id])
            ->post(route('notifications.markRead', $notification));

        $response->assertRedirect('/dashboard');
    }

    public function test_cannot_mark_other_user_notification(): void
    {
        $notification = Notification::create([
            'user_id' => $this->otherUser->id,
            'masjid_id' => $this->masjid->id,
            'type' => 'loan_overdue',
            'title' => 'Test',
            'message' => 'Test message',
        ]);

        $response = $this->actingAs($this->admin)
            ->withSession(['current_masjid_id' => $this->masjid->id])
            ->post(route('notifications.markRead', $notification));

        $response->assertStatus(403);
    }

    public function test_mark_all_as_read(): void
    {
        Notification::create([
            'user_id' => $this->admin->id,
            'masjid_id' => $this->masjid->id,
            'type' => 'loan_overdue',
            'title' => 'Test 1',
            'message' => 'Msg 1',
        ]);

        Notification::create([
            'user_id' => $this->admin->id,
            'masjid_id' => $this->masjid->id,
            'type' => 'feedback_new',
            'title' => 'Test 2',
            'message' => 'Msg 2',
        ]);

        $this->assertEquals(2, Notification::where('user_id', $this->admin->id)->whereNull('read_at')->count());

        $response = $this->actingAs($this->admin)
            ->withSession(['current_masjid_id' => $this->masjid->id])
            ->post(route('notifications.markAllRead'));

        $response->assertRedirect(route('notifications.index'));
        $this->assertEquals(0, Notification::where('user_id', $this->admin->id)->whereNull('read_at')->count());
    }

    public function test_unread_count_api(): void
    {
        Notification::create([
            'user_id' => $this->admin->id,
            'masjid_id' => $this->masjid->id,
            'type' => 'loan_overdue',
            'title' => 'Test',
            'message' => 'Msg',
        ]);

        Notification::create([
            'user_id' => $this->admin->id,
            'masjid_id' => $this->masjid->id,
            'type' => 'feedback_new',
            'title' => 'Test 2',
            'message' => 'Msg 2',
            'read_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson(route('notifications.unreadCount'));

        $response->assertOk();
        $response->assertJson(['count' => 1]);
    }

    public function test_notify_masjid_admins(): void
    {
        Notification::notifyMasjidAdmins(
            $this->masjid->id,
            'feedback_new',
            'Feedback Baru',
            'Test notification to admins',
            '/feedbacks'
        );

        // Admin gets notified
        $this->assertEquals(1, Notification::where('user_id', $this->admin->id)->count());
        // Operator does NOT get notified (only admins)
        $this->assertEquals(0, Notification::where('user_id', $this->otherUser->id)->count());
    }

    public function test_guest_cannot_access_notifications(): void
    {
        $response = $this->get(route('notifications.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_notification_external_link_blocked(): void
    {
        $notification = Notification::create([
            'user_id' => $this->admin->id,
            'masjid_id' => $this->masjid->id,
            'type' => 'loan_overdue',
            'title' => 'Malicious',
            'message' => 'Evil redirect',
            'link' => 'https://evil.example.com/phish',
        ]);

        $response = $this->actingAs($this->admin)
            ->withSession(['current_masjid_id' => $this->masjid->id])
            ->post(route('notifications.markRead', $notification));

        // Should redirect to notifications index, not external site
        $response->assertRedirect(route('notifications.index'));
    }
}
