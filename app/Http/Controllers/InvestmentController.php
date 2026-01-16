<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvestmentRequest;
use App\Http\Requests\UpdateInvestmentRequest;
use App\Models\Investment;
use App\Models\User;
use App\Services\AssetService;
use App\Services\ClientService;
use App\Services\InvestmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class InvestmentController extends Controller
{
    public function __construct(
        private readonly InvestmentService $investmentService,
        private readonly ClientService $clientService,
        private readonly AssetService $assetService
    ) {}

    public function index(): Response
    {
        /** @var User $user */
        $user = Auth::user();

        $clientId = request()->integer('client') ?: null;

        $investments = $this->investmentService->getInvestmentsByUser(
            $user->id,
            15,
            $clientId
        );
        $stats = $this->investmentService->getInvestmentStats($user->id, $clientId);

        $clients = $this->clientService->getClientsForSelect($user->id);
        $assets = $this->assetService->getAssetsForSelect();

        return Inertia::render('Investments/Index', [
            'investments' => $investments,
            'clients' => $clients,
            'assets' => $assets,
            'stats' => $stats,
            'filters' => [
                'client' => $clientId ? (string) $clientId : null,
            ],
        ]);
    }

    public function store(StoreInvestmentRequest $request): RedirectResponse
    {
        try {
            $this->investmentService->createInvestment($request->validated());

            return redirect()->back()->with('success', 'Investimento criado com sucesso!');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['investment' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->withErrors(['investment' => 'Erro ao criar investimento. Tente novamente.']);
        }
    }

    public function update(UpdateInvestmentRequest $request, Investment $investment): RedirectResponse
    {
        try {
            /** @var User $user */
            $user = $request->user();

            $client = $this->clientService->getClientById($investment->client_id);

            if ($client->user_id !== $user->id) {
                return back()->withErrors(['investment' => 'Você não tem permissão para editar este investimento.']);
            }

            $this->investmentService->updateInvestment($investment->id, $request->validated());

            return redirect()->back()->with('success', 'Investimento atualizado com sucesso!');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['investment' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->withErrors(['investment' => 'Erro ao atualizar investimento. Tente novamente.']);
        }
    }

    public function destroy(Investment $investment): RedirectResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            $client = $this->clientService->getClientById($investment->client_id);

            if ($client->user_id !== $user->id) {
                return back()->withErrors(['investment' => 'Você não tem permissão para excluir este investimento.']);
            }

            $this->investmentService->deleteInvestment($investment->id);

            return redirect()->back()->with('success', 'Investimento excluído com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['investment' => 'Erro ao excluir investimento. Tente novamente.']);
        }
    }
}
