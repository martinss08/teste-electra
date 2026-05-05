<script setup>
import { ref, onMounted, reactive } from 'vue'
import api from '../services/api.js'
import Swal from 'sweetalert2'


const items = ref([])
const loading = ref(false)
const errorMsg = ref('')
const successMsg = ref('')
const showForm = ref(false)
const isEditing = ref(false)
const submitting = ref(false)
const showViewModal = ref(false)
const viewingItem = ref(null)

const form = reactive({
    id: null,
    name: '',
    code: '',
    category: '',
    description: '',
    price: '',
    quantity: ''
})

const formErrors = reactive({ 
    name: '', 
    code: '',
    category: '',
    description: '', 
    price: '', 
    quantity: '' 
})

const categories = [
  'Eletrônicos',
  'Alimentos',
  'Vestuário',
  'Móveis',
  'Livros',
  'Brinquedos',
  'Ferramentas',
  'Outros'
]

function clearMessages() {
    errorMsg.value = '';
    successMsg.value = '' 
}
function clearFormErrors() {
    formErrors.name = '';
    formErrors.code = '';
    formErrors.category = '';
    formErrors.description = '';
    formErrors.price = '';
    formErrors.quantity = '';
}

function resetForm() {
    form.id = null;
    form.name = '';
    form.code = '';
    form.category = '';
    form.description = '';
    form.price = '';
    form.quantity = '';
  clearFormErrors()
}

const pagination = ref({})

const totalQuantity = ref(0)
const totalValue = ref(0)

async function fetchItems(page = 1) {
  loading.value = true
  clearMessages()

  try {
    const { data } = await api.get(`/items?page=${page}`)

    items.value = data.data.items.data
    pagination.value = data.data.items
    totalQuantity.value = data.data.summary?.total_quantity ?? 0
    totalValue.value = data.data.summary?.total_value ?? 0

  } catch (e) {
    errorMsg.value = e.response?.data?.message ?? 'Erro ao carregar itens.'
  } finally {
    loading.value = false
  }
}

function openCreateForm() {
    resetForm();
    isEditing.value = false;
    showForm.value = true;
    clearMessages()
}

function openEditForm(item) {
  form.id = item.id; 
  form.name = item.name; 
  form.code = item.code ?? '';
  form.category = item.category ?? '';
  form.description = item.description ?? ''
  form.price = item.price; 
  form.quantity = item.quantity
  clearFormErrors(); 
  isEditing.value = true; 
  showForm.value = true; 
  clearMessages()
}

function cancelForm() { showForm.value = false; resetForm() }

async function viewItem(item) {
  clearMessages()
  try {
    const { data } = await api.get(`/items/${item.id}`)
    viewingItem.value = data.data
    showViewModal.value = true
  } catch (e) {
    errorMsg.value = e.response?.data?.message ?? 'Erro ao carregar detalhes do item.'
  }
}

function closeViewModal() {
  showViewModal.value = false
  viewingItem.value = null
}

async function submitForm() {
  clearFormErrors(); clearMessages(); submitting.value = true
  try {
    const payload = {
      name: form.name,
      code: form.code || null,
      category: form.category || null,
      description: form.description || null,
      price: parseFloat(form.price),
      quantity: parseInt(form.quantity),
    }
    if (isEditing.value) {
      const { data } = await api.put(`/items/${form.id}`, payload)
      successMsg.value = data.message ?? 'Item atualizado com sucesso!'
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Item atualizado!',
        showConfirmButton: false,
        timer: 5000
      })
    } else {
      const { data } = await api.post('/items', payload)
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Item criado!',
        showConfirmButton: false,
        timer: 5000
      })
    }
    showForm.value = false
    resetForm()
    await fetchItems(pagination.value.current_page || 1)
  } catch (e) {
    if (e.response?.status === 422) {
      const errors = e.response.data.errors ?? {}
      formErrors.name = errors.name?.[0] ?? ''
      formErrors.code = errors.code?.[0] ?? ''
      formErrors.category = errors.category?.[0] ?? ''
      formErrors.description = errors.description?.[0] ?? ''
      formErrors.price = errors.price?.[0] ?? ''
      formErrors.quantity = errors.quantity?.[0] ?? ''
    } else {
      errorMsg.value = e.response?.data?.message ?? 'Erro ao salvar item.'
    }
  } finally {
    submitting.value = false
  }
}


function confirmDelete(item) {
  Swal.fire({
    title: 'Tem certeza?',
    text: `Excluir "${item.name}"?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Sim, excluir',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      deleteItem(item)
    }
  })
}

async function deleteItem(item) {
  try {
    await api.delete(`/items/${item.id}`)

    Swal.fire({
      icon: 'success',
      title: 'Excluído!',
      text: 'Item removido com sucesso',
      timer: 1500,
      showConfirmButton: false
    })

    // Recarregar a página atual ou voltar para a anterior se a atual ficar vazia
    let currentPage = pagination.value.current_page
    const itemsInPage = items.value.length
    
    // Se foi o último item da página e não é a primeira página, voltar uma página
    if (itemsInPage === 1 && currentPage > 1) {
      currentPage = currentPage - 1
    }
    
    await fetchItems(currentPage)

  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Erro',
      text: 'Não foi possível excluir'
    })
  }
}

function formatPrice(value) {
  return Number(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

onMounted(fetchItems)
</script>

<template>
  <nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
      <span class="navbar-brand fw-bold">Gerenciador de Itens</span>
    </div>
  </nav>

  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">Itens cadastrados</h5>
      <button class="btn btn-primary" @click="openCreateForm">Novo Item</button>
    </div>

    <div v-if="successMsg" class="alert alert-success alert-dismissible">
      {{ successMsg }}
      <button type="button" class="btn-close" @click="successMsg = ''"></button>
    </div>
    <div v-if="errorMsg" class="alert alert-danger alert-dismissible">
      {{ errorMsg }}
      <button type="button" class="btn-close" @click="errorMsg = ''"></button>
    </div>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-2 text-muted">Carregando...</p>
    </div>

    <div v-else-if="items.length === 0 && !errorMsg" class="text-center py-5 text-muted">
      <p>Nenhum item cadastrado.</p>
      <button class="btn btn-outline-primary" @click="openCreateForm">Cadastrar primeiro item</button>
    </div>

    <div v-else-if="items.length > 0">
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="card text-center">
            <div class="card-body">
              <div class="text-muted small">Total de itens</div>
              <div class="fs-4 fw-bold">{{ pagination.total }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-center">
            <div class="card-body">
              <div class="text-muted small">Quantidade em estoque</div>
              <div class="fs-4 fw-bold">{{ totalQuantity }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-center">
            <div class="card-body">
              <div class="text-muted small">Valor em estoque</div>
              <div class="fs-4 fw-bold">{{ formatPrice(totalValue) }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Nome</th>
              <th>Código</th>
              <th>Categoria</th>
              <th>Preço</th>
              <th>Quantidade</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td class="text-muted">{{ item.id }}</td>
              <td class="fw-semibold">{{ item.name }}</td>
              <td class="text-muted">{{ item.code || '—' }}</td>
              <td>{{ item.category || '—' }}</td>
              <td>{{ formatPrice(item.price) }}</td>
              <td>
                <span class="badge bg-secondary">
                  {{ item.quantity }}
                </span>
              </td>
              <td>
                <button class="btn btn-sm btn-outline-primary me-1" @click="viewItem(item)">Visualizar</button>
                <button class="btn btn-sm btn-outline-secondary me-1" @click="openEditForm(item)">Editar</button>
                <button class="btn btn-sm btn-outline-danger" @click="confirmDelete(item)">Excluir</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginação -->
      <nav class="mt-3" v-if="pagination.last_page > 1">
        <ul class="pagination justify-content-center">
          <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
            <button class="page-link" @click="fetchItems(pagination.current_page - 1)" :disabled="pagination.current_page === 1">
              Anterior
            </button>
          </li>

          <li 
            v-for="page in pagination.last_page" 
            :key="page"
            class="page-item"
            :class="{ active: page === pagination.current_page }"
          >
            <button class="page-link" @click="fetchItems(page)">
              {{ page }}
            </button>
          </li>

          <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
            <button class="page-link" @click="fetchItems(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page">
              Próximo
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </div>

  <!-- Modal -->
  <div v-if="showForm" class="modal d-block" tabindex="-1" @click.self="cancelForm" style="background:rgba(0,0,0,0.5)">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ isEditing ? 'Editar Item' : 'Novo Item' }}</h5>
          <button type="button" class="btn-close" @click="cancelForm"></button>
        </div>
        <form @submit.prevent="submitForm">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nome <span class="text-danger">*</span></label>
              <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': formErrors.name }" placeholder="Nome do item" />
              <div v-if="formErrors.name" class="invalid-feedback">{{ formErrors.name }}</div>
            </div>
            <div class="row g-3 mb-3">
              <div class="col-6">
                <label class="form-label">Código</label>
                <input v-model="form.code" type="text" class="form-control" :class="{ 'is-invalid': formErrors.code }" placeholder="Código do item (opcional)" />
                <div v-if="formErrors.code" class="invalid-feedback">{{ formErrors.code }}</div>
              </div>
              <div class="col-6">
                <label class="form-label">Categoria</label>
                <select v-model="form.category" class="form-select" :class="{ 'is-invalid': formErrors.category }">
                  <option value="">Selecione </option>
                  <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                </select>
                <div v-if="formErrors.category" class="invalid-feedback">{{ formErrors.category }}</div>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Descrição</label>
              <textarea v-model="form.description" class="form-control" :class="{ 'is-invalid': formErrors.description }" rows="3" placeholder="Descrição opcional"></textarea>
              <div v-if="formErrors.description" class="invalid-feedback">{{ formErrors.description }}</div>
            </div>
            <div class="row g-3">
              <div class="col-6">
                <label class="form-label">Preço (R$) <span class="text-danger">*</span></label>
                <input v-model="form.price" type="number" class="form-control" :class="{ 'is-invalid': formErrors.price }" step="0.01" min="0" placeholder="0,00" />
                <div v-if="formErrors.price" class="invalid-feedback">{{ formErrors.price }}</div>
              </div>
              <div class="col-6">
                <label class="form-label">Quantidade <span class="text-danger">*</span></label>
                <input v-model="form.quantity" type="number" class="form-control" :class="{ 'is-invalid': formErrors.quantity }" min="0" placeholder="0" />
                <div v-if="formErrors.quantity" class="invalid-feedback">{{ formErrors.quantity }}</div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="cancelForm">Cancelar</button>
            <button type="submit" class="btn btn-primary" :disabled="submitting">
              <span v-if="submitting" class="spinner-border spinner-border-sm me-1"></span>
              {{ submitting ? 'Salvando...' : (isEditing ? 'Atualizar' : 'Criar') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal de Visualização -->
  <div v-if="showViewModal && viewingItem" class="modal d-block" tabindex="-1" @click.self="closeViewModal" style="background:rgba(0,0,0,0.5)">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Detalhes do Item</h5>
          <button type="button" class="btn-close btn-close-white" @click="closeViewModal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="card border-0 bg-light">
                <div class="card-body">
                  <small class="text-muted d-block mb-1">ID</small>
                  <strong>{{ viewingItem.id }}</strong>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card border-0 bg-light">
                <div class="card-body">
                  <small class="text-muted d-block mb-1">Nome</small>
                  <strong>{{ viewingItem.name }}</strong>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card border-0 bg-light">
                <div class="card-body">
                  <small class="text-muted d-block mb-1">Código</small>
                  <strong>{{ viewingItem.code || '—' }}</strong>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card border-0 bg-light">
                <div class="card-body">
                  <small class="text-muted d-block mb-1">Categoria</small>
                  <strong>{{ viewingItem.category || '—' }}</strong>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card border-0 bg-light">
                <div class="card-body">
                  <small class="text-muted d-block mb-1">Preço</small>
                  <strong class="text-success">{{ formatPrice(viewingItem.price) }}</strong>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card border-0 bg-light">
                <div class="card-body">
                  <small class="text-muted d-block mb-1">Quantidade</small>
                  <strong>
                    <span class="badge bg-secondary">{{ viewingItem.quantity }}</span>
                  </strong>
                </div>
              </div>
            </div>
            <div class="col-12">
              <div class="card border-0 bg-light">
                <div class="card-body">
                  <small class="text-muted d-block mb-1">Descrição</small>
                  <p class="mb-0">{{ viewingItem.description || '—' }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeViewModal">Fechar</button>
        </div>
      </div>
    </div>
  </div>
</template>
