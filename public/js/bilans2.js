const removeModal = new bootstrap.Modal(document.querySelector('#remove-modal'));
const removeItemBtn = document.querySelector('#remove-btn');

const confirmationModal = new bootstrap.Modal(document.querySelector('#confirmation-modal'));
const confirmationTitle = document.querySelector('#confirmation-title');
const confirmationBody = document.querySelector('#confirmation-body');

const removeItem = async (item, id) => {
        removeModal.show();
        removeItemBtn.addEventListener('click', () => confirmRemoveItems(item, id));
        console.error(data.message);
}

const confirmRemoveItems = async (item, id) => {

    try {
        const res = await fetch(`../bilans/remove-${item}/${id}`);;
        const data = await res.json();

        if (data.message_type == 'success') {
            sessionStorage.setItem('action', 'remove');
            location.reload();
        }
    } catch (data) {
        console.error(data.message);
    }
}

const showConfirmation = () => {
    const action = sessionStorage.getItem('action', 'remove');

    if (action) {
        prepareConfirmation(action);
        confirmationModal.show();
        sessionStorage.removeItem('action');
    }
}

const prepareConfirmation = action => {

        confirmationTitle.innerText = `Wartość usunięta`;
        confirmationBody.innerText = `Wartość odpowiednio  usunięta!`;
}

document.addEventListener('DOMContentLoaded', showConfirmation);