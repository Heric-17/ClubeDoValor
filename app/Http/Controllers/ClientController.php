<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Models\User;
use App\Services\ClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function __construct(
        private readonly ClientService $clientService
    ) {}

    public function index(): Response
    {
        /** @var User $user */
        $user = Auth::user();

        $clients = $this->clientService->getClientsByUser($user->id);

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
        ]);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        try {
            /** @var User $user */
            $user = $request->user();

            $data = $request->validated();
            $data['user_id'] = $user->id;

            $this->clientService->createClient($data);

            return redirect()->back()->with('success', 'Cliente criado com sucesso!');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['client' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->withErrors(['client' => 'Erro ao criar cliente. Tente novamente.']);
        }
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        try {
            /** @var User $user */
            $user = $request->user();

            if ($client->user_id !== $user->id) {
                return back()->withErrors(['client' => 'Você não tem permissão para editar este cliente.']);
            }

            $this->clientService->updateClient($client->id, $request->validated());

            return redirect()->back()->with('success', 'Cliente atualizado com sucesso!');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['client' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->withErrors(['client' => 'Erro ao atualizar cliente. Tente novamente.']);
        }
    }

    public function destroy(Client $client): RedirectResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            if ($client->user_id !== $user->id) {
                return back()->withErrors(['client' => 'Você não tem permissão para excluir este cliente.']);
            }

            $this->clientService->deleteClient($client->id);

            return redirect()->back()->with('success', 'Cliente excluído com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['client' => 'Erro ao excluir cliente. Tente novamente.']);
        }
    }
}
