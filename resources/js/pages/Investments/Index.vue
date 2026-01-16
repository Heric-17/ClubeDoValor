<script setup>
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

import DangerButton from '@/components/DangerButton.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import Modal from '@/components/Modal.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

const props = defineProps({
    investments: {
        type: Object,
        required: true,
    },
    clients: {
        type: Array,
        required: true,
    },
    assets: {
        type: Array,
        required: true,
    },
    stats: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const selectedClient = ref(props.filters.client || '');

const page = usePage();
const showModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const editingInvestment = ref(null);
const deletingInvestment = ref(null);

const form = useForm({
    client_id: '',
    asset_id: '',
    amount: '',
    investment_date: new Date().toISOString().split('T')[0],
});

const editForm = useForm({
    client_id: '',
    asset_id: '',
    amount: '',
    investment_date: '',
});

const amountDisplay = ref('');
const editAmountDisplay = ref('');

const applyCurrencyMask = (value) => {
    const numbers = value.replace(/\D/g, '');
    
    if (!numbers) return '';
    
    const amount = parseFloat(numbers) / 100;
    
    return new Intl.NumberFormat('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(amount);
};

const removeCurrencyMask = (value) => {
    if (!value) return '';
    const numbers = value.replace(/\D/g, '');
    return (parseFloat(numbers) / 100).toString();
};

const handleAmountInput = (event) => {
    const value = event.target.value;
    amountDisplay.value = applyCurrencyMask(value);
    form.amount = removeCurrencyMask(value);
};

watch(() => form.amount, (newValue) => {
    if (!newValue || newValue === '') {
        amountDisplay.value = '';
    } else if (newValue !== removeCurrencyMask(amountDisplay.value)) {
        amountDisplay.value = applyCurrencyMask(newValue);
    }
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR');
};

const getAssetTypeLabel = (type) => {
    return type === 'FIXED' ? 'Renda Fixa' : 'Renda Variável';
};

const submit = () => {
    form.post(route('investments.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            amountDisplay.value = '';
            showModal.value = false;
        },
    });
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
    amountDisplay.value = '';
};

const openEditModal = (investment) => {
    editingInvestment.value = investment;
    editForm.client_id = investment.client_id.toString();
    editForm.asset_id = investment.asset_id.toString();
    editForm.amount = investment.amount.toString();
    editForm.investment_date = investment.investment_date;
    editAmountDisplay.value = applyCurrencyMask(investment.amount.toString());
    showEditModal.value = true;
};

const submitEdit = () => {
    editForm.put(route('investments.update', editingInvestment.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            editForm.reset();
            editAmountDisplay.value = '';
            showEditModal.value = false;
            editingInvestment.value = null;
        },
    });
};

const closeEditModal = () => {
    showEditModal.value = false;
    editForm.reset();
    editForm.clearErrors();
    editAmountDisplay.value = '';
    editingInvestment.value = null;
};

const openDeleteModal = (investment) => {
    deletingInvestment.value = investment;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    router.delete(route('investments.destroy', deletingInvestment.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            deletingInvestment.value = null;
        },
    });
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    deletingInvestment.value = null;
};

const handleEditAmountInput = (event) => {
    const value = event.target.value;
    editAmountDisplay.value = applyCurrencyMask(value);
    editForm.amount = removeCurrencyMask(value);
};

watch(() => editForm.amount, (newValue) => {
    if (!newValue || newValue === '') {
        editAmountDisplay.value = '';
    } else if (newValue !== removeCurrencyMask(editAmountDisplay.value)) {
        editAmountDisplay.value = applyCurrencyMask(newValue);
    }
});

const filterByClient = () => {
    router.get(route('investments.index'), {
        client: selectedClient.value || null,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Investimentos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Investimentos
                </h2>
                <PrimaryButton @click="showModal = true">
                    Novo Aporte
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <InputLabel for="filter_client" value="Filtrar por Cliente" />
                                <select
                                    id="filter_client"
                                    v-model="selectedClient"
                                    @change="filterByClient"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Todos os clientes</option>
                                    <option
                                        v-for="client in clients"
                                        :key="client.id"
                                        :value="client.id"
                                    >
                                        {{ client.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6 grid gap-6 md:grid-cols-2">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">
                                Total do Mês
                            </div>
                            <div class="mt-2 text-3xl font-bold text-gray-900">
                                {{ formatCurrency(stats.total_current_month) }}
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">
                                Top Ativo
                            </div>
                            <div class="mt-2 text-3xl font-bold text-gray-900">
                                {{ stats.top_asset || 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Data
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Cliente
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Ativo
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Tipo
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Valor
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-if="investments.data.length === 0">
                                        <td
                                            colspan="6"
                                            class="px-6 py-4 text-center text-sm text-gray-500"
                                        >
                                            Nenhum investimento encontrado.
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="investment in investments.data"
                                        :key="investment.id"
                                        class="hover:bg-gray-50"
                                    >
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            {{ formatDate(investment.investment_date) }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            {{ investment.client.name }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            {{ investment.asset.symbol }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            {{ getAssetTypeLabel(investment.asset.type) }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ formatCurrency(investment.amount) }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                            <button
                                                @click="openEditModal(investment)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3"
                                            >
                                                Editar
                                            </button>
                                            <button
                                                @click="openDeleteModal(investment)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Excluir
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div
                            v-if="investments.links.length > 3"
                            class="mt-4 flex items-center justify-between"
                        >
                            <div class="text-sm text-gray-700">
                                Mostrando
                                <span class="font-medium">{{ investments.from }}</span>
                                até
                                <span class="font-medium">{{ investments.to }}</span>
                                de
                                <span class="font-medium">{{ investments.total }}</span>
                                resultados
                            </div>
                            <div class="flex space-x-2">
                                <template
                                    v-for="(link, index) in investments.links"
                                    :key="index"
                                >
                                    <Link
                                        v-if="link.url"
                                        :href="link.url"
                                        :class="{
                                            'bg-gray-800 text-white': link.active,
                                            'bg-white text-gray-700 hover:bg-gray-50': !link.active,
                                        }"
                                        class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium"
                                    >
                                        <span v-html="link.label" />
                                    </Link>
                                    <span
                                        v-else
                                        :class="{
                                            'bg-gray-200 text-gray-400': true,
                                        }"
                                        class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium"
                                        v-html="link.label"
                                    />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Novo Aporte
                </h2>

                <form @submit.prevent="submit" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="client_id" value="Cliente" />
                        <select
                            id="client_id"
                            v-model="form.client_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            <option value="">Selecione um cliente</option>
                            <option
                                v-for="client in clients"
                                :key="client.id"
                                :value="client.id"
                            >
                                {{ client.name }}
                            </option>
                        </select>
                        <InputError
                            class="mt-2"
                            :message="form.errors.client_id"
                        />
                    </div>

                    <div>
                        <InputLabel for="asset_id" value="Ativo" />
                        <select
                            id="asset_id"
                            v-model="form.asset_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            <option value="">Selecione um ativo</option>
                            <option
                                v-for="asset in assets"
                                :key="asset.id"
                                :value="asset.id"
                            >
                                {{ asset.symbol }} - {{ asset.name }}
                            </option>
                        </select>
                        <InputError
                            class="mt-2"
                            :message="form.errors.asset_id"
                        />
                    </div>

                    <div>
                        <InputLabel for="amount" value="Valor (R$)" />
                        <input
                            id="amount"
                            :value="amountDisplay"
                            type="text"
                            inputmode="numeric"
                            placeholder="0,00"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            @input="handleAmountInput"
                            required
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.amount"
                        />
                    </div>

                    <div>
                        <InputLabel for="investment_date" value="Data do Investimento" />
                        <TextInput
                            id="investment_date"
                            v-model="form.investment_date"
                            type="date"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.investment_date"
                        />
                    </div>

                    <div v-if="form.errors.investment">
                        <InputError :message="form.errors.investment" />
                    </div>

                    <div
                        v-if="page.props.flash?.success"
                        class="rounded-md bg-green-50 p-4"
                    >
                        <p class="text-sm font-medium text-green-800">
                            {{ page.props.flash.success }}
                        </p>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <SecondaryButton type="button" @click="closeModal">
                            Cancelar
                        </SecondaryButton>
                        <PrimaryButton
                            type="submit"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Salvando...' : 'Salvar' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal :show="showEditModal" @close="closeEditModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Editar Aporte
                </h2>

                <form @submit.prevent="submitEdit" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="edit_client_id" value="Cliente" />
                        <select
                            id="edit_client_id"
                            v-model="editForm.client_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            <option value="">Selecione um cliente</option>
                            <option
                                v-for="client in clients"
                                :key="client.id"
                                :value="client.id"
                            >
                                {{ client.name }}
                            </option>
                        </select>
                        <InputError
                            class="mt-2"
                            :message="editForm.errors.client_id"
                        />
                    </div>

                    <div>
                        <InputLabel for="edit_asset_id" value="Ativo" />
                        <select
                            id="edit_asset_id"
                            v-model="editForm.asset_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            <option value="">Selecione um ativo</option>
                            <option
                                v-for="asset in assets"
                                :key="asset.id"
                                :value="asset.id"
                            >
                                {{ asset.symbol }} - {{ asset.name }}
                            </option>
                        </select>
                        <InputError
                            class="mt-2"
                            :message="editForm.errors.asset_id"
                        />
                    </div>

                    <div>
                        <InputLabel for="edit_amount" value="Valor (R$)" />
                        <input
                            id="edit_amount"
                            :value="editAmountDisplay"
                            @input="handleEditAmountInput"
                            type="text"
                            inputmode="numeric"
                            placeholder="0,00"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        />
                        <InputError
                            class="mt-2"
                            :message="editForm.errors.amount"
                        />
                    </div>

                    <div>
                        <InputLabel for="edit_investment_date" value="Data do Investimento" />
                        <TextInput
                            id="edit_investment_date"
                            v-model="editForm.investment_date"
                            type="date"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            class="mt-2"
                            :message="editForm.errors.investment_date"
                        />
                    </div>

                    <div v-if="editForm.errors.investment">
                        <InputError :message="editForm.errors.investment" />
                    </div>

                    <div class="flex justify-end space-x-3">
                        <SecondaryButton type="button" @click="closeEditModal">
                            Cancelar
                        </SecondaryButton>
                        <PrimaryButton
                            type="submit"
                            :disabled="editForm.processing"
                        >
                            {{ editForm.processing ? 'Salvando...' : 'Salvar' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal :show="showDeleteModal" @close="closeDeleteModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Excluir Aporte
                </h2>

                <p class="mt-4 text-sm text-gray-600">
                    Tem certeza que deseja excluir este aporte? Esta ação não pode ser desfeita.
                </p>

                <div v-if="deletingInvestment" class="mt-4 rounded-md bg-gray-50 p-4">
                    <p class="text-sm text-gray-900">
                        <strong>Cliente:</strong> {{ deletingInvestment.client.name }}
                    </p>
                    <p class="text-sm text-gray-900">
                        <strong>Ativo:</strong> {{ deletingInvestment.asset.symbol }}
                    </p>
                    <p class="text-sm text-gray-900">
                        <strong>Valor:</strong> {{ formatCurrency(deletingInvestment.amount) }}
                    </p>
                    <p class="text-sm text-gray-900">
                        <strong>Data:</strong> {{ formatDate(deletingInvestment.investment_date) }}
                    </p>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton type="button" @click="closeDeleteModal">
                        Cancelar
                    </SecondaryButton>
                    <DangerButton
                        type="button"
                        @click="confirmDelete"
                    >
                        Excluir
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
