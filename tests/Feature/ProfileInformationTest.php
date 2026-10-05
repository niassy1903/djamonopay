<?php

namespace Tests\Feature;

use App\Models\Users2;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileInformationTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_profile_information_is_available(): void
    {
        $this->actingAs($user = Users2::factory()->create());

        $component = Livewire::test(UpdateProfileInformationForm::class);

        $this->assertEquals($user->name, $component->state['name']);
        $this->assertEquals($user->email, $component->state['email']);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $this->actingAs($user = Users2::factory()->create());

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['name' => 'Test Name', 'email' => 'test@example.com'])
            ->call('updateProfileInformation');

        $this->assertEquals('Test Name', $user->fresh()->name);
        $this->assertEquals('test@example.com', $user->fresh()->email);
    }

    public function test_all_profile_fields_can_be_updated(): void
    {
        $this->actingAs($user = Users2::factory()->create());

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state.prenom', 'Aminata')
            ->set('state.nom', 'Diallo')
            ->set('state.email', 'aminata@example.com')
            ->set('state.telephone', '771234567')
            ->set('state.adresse', 'Dakar, Sénégal')
            ->set('state.date_naissance', '1992-04-12')
            ->set('state.numero_identite', 'ID-UPDATED-001')
            ->call('updateProfileInformation')
            ->assertHasNoErrors();

        $this->assertSame('Aminata Diallo', $user->fresh()->name);
        $this->assertSame('771234567', $user->fresh()->telephone);
        $this->assertSame('ID-UPDATED-001', $user->fresh()->numero_identite);
    }

    public function test_profile_page_shows_view_edit_and_logout_actions(): void
    {
        $user = Users2::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('Informations du profil')
            ->assertSee('Modifier mon profil')
            ->assertSee('Téléphone')
            ->assertSee(route('logout'));

        $this->post(route('logout'))->assertRedirect('/');
        $this->assertGuest();
    }
}
