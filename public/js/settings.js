const addEditModal = document.querySelector('#add-edit-modal');
const addEditTitle = document.querySelector('#add-edit-title');
const addEditForm = document.querySelector('#add-edit-form');
const newCategoryName = document.querySelector('#new-category-name');
const addEditLabel = document.querySelector('#add-edit-label');
const firstformElement = document.querySelector('#first-form-element');
const secondformElement = document.querySelector('#second-form-element');
const addEditMessage = document.querySelector('#add-edit-message');
const submitFormBtn = document.querySelector('#submit-form-btn');

const removeEntriesModal = new bootstrap.Modal(document.querySelector('#remove-entries-modal'));
const removeCategoryBtn = document.querySelector('#remove-category-btn');

const confirmationModal = new bootstrap.Modal(document.querySelector('#confirmation-modal'));
const confirmationTitle = document.querySelector('#confirmation-title');
const confirmationBody = document.querySelector('#confirmation-body');
    

const prepareForm = (action, category, id, name) => {

    addEditModal.setAttribute('data-action', action);
    addEditModal.setAttribute('data-category', category);
    addEditModal.setAttribute('data-name', name);

    if (newCategoryName !== '') {
        newCategoryName.value = name;
    }

    if (id !== 0) {
        addEditModal.setAttribute('data-id', id);
    }

    if (action == 'add') {
        addEditTitle.innerText = `Dodawanie kategorii`;
        addEditLabel.innerText = `Wpisz nazwę kategorii:`;
        } else {
            addEditTitle.innerText = `Edytowanie kategorii`;
            addEditLabel.innerText = `Edytuj nazwę kategorii:`;
        }
    submitFormBtn.innerText = `Zatwierdź`;

    if (category == 'expense') {
        getLimit(name);
    }
}

const setLimit = async (name) => {
    try {
        const res = await fetch(`../api/limit/${name}`);
        const data = await res.json();
        return data;
    } catch (e) {
        console.log('ERROR', e);
    }
}

const getLimit = async (name) => {

    if (name !== '') {
        const limitInfoData = await setLimit(name);
        const limit= limitInfoData.cash_limit;
        const isLimitActive = limitInfoData.is_limit_active;

        if (limit === 0 && isLimitActive === 0) {
            appendExpenseElements();
        } else {
            appendExpenseElements(limit, isLimitActive);
        }
    } else {
        appendExpenseElements();
    }
}

const appendExpenseElements = (limit, isLimitActive) => {

    const formcheck = document.createElement('div');
    const checkBoxInput = document.createElement('input');
    const checkBoxLabel = document.createElement('label');
    const input = document.createElement('input');

    formcheck.setAttribute('class', 'form-check settings-checkbox additional');

    checkBoxInput.setAttribute('class', 'form-check-input');
    checkBoxInput.setAttribute('id', 'modal-checkbox');
    checkBoxInput.setAttribute('type', 'checkbox');
    checkBoxInput.setAttribute('onclick', 'activateLimitField(this)');

    checkBoxLabel.setAttribute('class', 'form-check-label');
    checkBoxLabel.setAttribute('for', 'modal-checkbox');
    checkBoxLabel.innerText = 'Aktywuj limit';

    input.setAttribute('class', 'form-control additional');
    input.setAttribute('id', 'category-limit');
    input.setAttribute('type', 'number');
    input.setAttribute('name', 'category-limit');
    input.setAttribute('min', '0');
    input.setAttribute('step', '0.01');
    input.setAttribute('value', '');
    input.disabled = true;
    
    if (!! limit && !!isLimitActive) {

        if (checkBoxInput.checked = true) {
            input.disabled = false;
            input.value = limit;
        }
        else {
            input.disabled = false;
            input.value = '';
        }
    } else {
        checkBoxInput.checked = false;
        input.disabled = true;
        input.value = '';
    }

    firstformElement.appendChild(formcheck);
    secondformElement.appendChild(input);

    formcheck.appendChild(checkBoxInput);
    formcheck.appendChild(checkBoxLabel);
    
}

const activateLimitField = function (element) {
    const limit = document.querySelector('#category-limit');
    if (element.checked) {
        limit.disabled = false;
        limit.value = 0;
    } else {
        limit.disabled = true;
        limit.value = '';
    }
}

const handleFormSubmit = async function (e) {
    const action = addEditModal.getAttribute('data-action');
    const category = addEditModal.getAttribute('data-category');
    const id = addEditModal.getAttribute('data-id');

    e.preventDefault();

    let formData = new FormData(this);

    try {
        const res = await fetch(`../settings/${action}-${category}-category${id ? '/' + id : ''}`, {
            method: 'POST',
            body: formData
        });

        if (res.ok) {
            const data = await res.json();

            if (data.message_type == 'error') {
                addEditMessage.innerText = data.message;
            }

            if (data.message_type == 'success') {
                sessionStorage.setItem('action', `${action}`);
                location.reload();
            }
        } else {
            throw new Error('Request failed. Status: ' + res.status);
        }
    } catch (error) {
        console.error(error);
    }
}

const checkRemoveCategory = async (category, categoryId) => {
    try {
        const res = await fetch(`../settings/check-remove-${category}-category/${categoryId}`);
        const data = await res.json();

        if (data.message_type == 'warning') {
            removeEntriesModal.show();
            removeCategoryBtn.addEventListener('click', () => removeCategory(category, categoryId));
        }

        if (data.message_type == 'success') {
            sessionStorage.setItem('action', 'remove');
            location.reload();
        }
    } catch (data) {
        console.error(data.message);
    }
}

const removeCategory = async (category, categoryId) => {
    try {
        const res = await fetch(`../settings/remove-${category}-category/${categoryId}`);
        const data = await res.json();

        if (data.message_type == 'success') {
            sessionStorage.setItem('action', 'remove');
            location.reload();
        }
    } catch (data) {
        console.error(data.message);
    }
}

const clearInput = () => {
    addEditForm.reset();
    addEditMessage.innerText = '';

    const elements = document.querySelectorAll('.additional');

    elements.forEach(element => {
        element.remove();
    });
}

const showConfirmation = () => {
    const action = sessionStorage.getItem('action');

    if (action) {
        prepareConfirmation(action);
        confirmationModal.show();
        sessionStorage.removeItem('action');
    }
}

const prepareConfirmation = action => {

    if (action == 'add') {
        confirmationTitle.innerText = `Kategoria dodana`;
        confirmationBody.innerText = `Kategoria odpowiednio  dodana!`;
    } else if (action == 'edit'){
        confirmationTitle.innerText = `Kategoria edytowana`;
        confirmationBody.innerText = `Kategoria odpowiednio  edytowana!`;
    }
    else  {
        confirmationTitle.innerText = `Kategoria usunięta`;
        confirmationBody.innerText = `Kategoria odpowiednio  usunięta!`;
    }
}

addEditForm.addEventListener('submit', handleFormSubmit);
addEditModal.addEventListener('hidden.bs.modal', clearInput);
document.addEventListener('DOMContentLoaded', showConfirmation);