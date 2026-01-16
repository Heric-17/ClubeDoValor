<script setup>
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

import DangerButton from '@/components/DangerButton.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import Modal from '@/components/Modal.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

defineProps({
    clients: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const showModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const editingClient = ref(null);
const deletingClient = ref(null);

const form = useForm({
    name: '',
    email: '',
});

const editForm = useForm({
    name: '',
    email: '',
});

const submit = () => {
    form.post(route('clients.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showModal.value = false;
        },
    });
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
};

const openEditModal = (client) => {
    editingClient.value = client;
    editForm.name = client.name;
    editForm.email = client.email;
    showEditModal.value = true;
};

const submitEdit = () => {
    editForm.put(route('clients.update', editingClient.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            editForm.reset();
            showEditModal.value = false;
            editingClient.value = null;
        },
    });
};

const closeEditModal = () => {
    showEditModal.value = false;
    editForm.reset();
    editForm.clearErrors();
    editingClient.value = null;
};

const openDeleteModal = (client) => {
    deletingClient.value = client;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    router.delete(route('clients.destroy', deletingClient.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            deletingClient.value = null;
        },
    });
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    deletingClient.value = null;
};
</script>

<template>
    <Head title="Clientes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Clientes
                </h2>
                <PrimaryButton @click="showModal = true">
                    Novo Cliente
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Nome
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Email
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-if="clients.length === 0">
                                        <td
                                            colspan="3"
                                            class="px-6 py-4 text-center text-sm text-gray-500"
                                        >
                                            Nenhum cliente encontrado.
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="client in clients"
                                        :key="client.id"
                                        class="hover:bg-gray-50"
                                    >
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            {{ client.name }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            {{ client.email }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                            <div class="flex items-center justify-end gap-3">
                                                <Link
                                                    :href="route('investments.index', { client: client.id })"
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                >
                                                    Ver Aportes
                                                </Link>
                                                <button
                                                    @click="openEditModal(client)"
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                >
                                                    Editar
                                                </button>
                                                <button
                                                    @click="openDeleteModal(client)"
                                                    class="text-red-600 hover:text-red-900"
                                                >
                                                    Excluir
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Novo Cliente
                </h2>

                <form @submit.prevent="submit" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="name" value="Nome" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autofocus
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.name"
                        />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.email"
                        />
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
                    Editar Cliente
                </h2>

                <form @submit.prevent="submitEdit" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="edit_name" value="Nome" />
                        <TextInput
                            id="edit_name"
                            v-model="editForm.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autofocus
                        />
                        <InputError
                            class="mt-2"
                            :message="editForm.errors.name"
                        />
                    </div>

                    <div>
                        <InputLabel for="edit_email" value="Email" />
                        <TextInput
                            id="edit_email"
                            v-model="editForm.email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            class="mt-2"
                            :message="editForm.errors.email"
                        />
                    </div>

                    <div v-if="editForm.errors.client">
                        <InputError :message="editForm.errors.client" />
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
                    Excluir Cliente
                </h2>

                <p class="mt-4 text-sm text-gray-600">
                    Tem certeza que deseja excluir este cliente? Esta ação não pode ser desfeita.
                </p>

                <div v-if="deletingClient" class="mt-4 rounded-md bg-gray-50 p-4">
                    <p class="text-sm text-gray-900">
                        <strong>Nome:</strong> {{ deletingClient.name }}
                    </p>
                    <p class="text-sm text-gray-900">
                        <strong>Email:</strong> {{ deletingClient.email }}
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
