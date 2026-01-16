<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvestmentRequest;
use App\Models\Asset;
use App\Models\Client;
use App\Models\User;
use App\Services\InvestmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class InvestmentController extends Controller
{
    public function __construct(
        private readonly InvestmentService $investmentService
    ) {}

    public function index(): Response
    {
        /** @var User $user */
        $user = Auth::user();

        $investments = $this->investmentService->getInvestmentsByUser($user->id);
        $stats = $this->investmentService->getInvestmentStats($user->id);

        $clients = Client::where('user_id', $user->id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $assets = Asset::select('id', 'symbol', 'name')
            ->orderBy('symbol')
            ->get();

        return Inertia::render('Investments/Index', [
            'investments' => $investments,
            'clients' => $clients,
            'assets' => $assets,
            'stats' => $stats,
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
}
