// Const declarations
const amountField = document.querySelector('#amount');
const dateField = document.querySelector('#date');
const categoryField = document.querySelector('#category');

const limitInfo = document.querySelector('#limit-info');
const limitValue = document.querySelector('#limit-value');
const limitLeft = document.querySelector('#limit-left');

// Rendering alert boxes
const renderInfoBox = limitInfoData => {
    const limit = limitInfoData.cash_limit;
    const isLimitActive = limitInfoData.is_limit_active;

    if (limit > 0) {
        limitInfo.innerText = `Ustaliłeś limit ${limit} PLN dla danej kategorii.`;
    } else {
        limitInfo.innerText = 'Limit nie został ustalony.';
    }   
}

const renderValueBox = monthlyExpenses => {
    limitValue.innerText = !!monthlyExpenses ? `Wydałeś ${monthlyExpenses} PLN w tym miesiącu na daną kategorię.` : `Nie wydałeś jeszcze żadnej kwoty w tej kategorii!`;
}

const renderLeftBox = (limitInfoData, monthlyExpenses, amount) => {
    const limit = limitInfoData.cash_limit;
    const isLimitActive = limitInfoData.is_limit_active;
    

    if (isLimitActive) {
        limitLeft.innerText = `Limit: ${(limit - monthlyExpenses - amount)} PLN.`;

        (limit - monthlyExpenses - amount) < 0 ? limitLeft.classList.add('above-limit') : limitLeft.classList.remove('above-limit');
    }
}

const limitLeftClear = () => {
    limitLeft.classList.remove('above-limit');
    limitLeft.innerText = 'Limit nie został ustalony lub nie wpisano kwoty wydatku.';
}

// Async fetch funtcions
const getLimitForCategory = async (category) => {
    try {
        const res = await fetch(`../api/limit/${category}`);
        const data = await res.json();
        return data;
    } catch (e) {
        console.log('ERROR', e);
    }
}

const getMonthlyExpenses = async (category, date) => {
    try {
        const res = await fetch(`../api/limitSum/${category}/${date}`);
        const data = await res.json();
        return data;
    } catch (e) {
        console.log('ERROR', e);
    }
}

// Events logic
const eventsAction = async (category, date, amount) => {
    if (!!category) {
        const limitInfoData = await getLimitForCategory(category);
        renderInfoBox(limitInfoData);

        if (!!date) {
            const monthlyExpenses = await getMonthlyExpenses(category, date);
            renderValueBox(monthlyExpenses);

            if (!!amount && !!limitInfoData) {
                renderLeftBox(limitInfoData, monthlyExpenses, amount);
            } else {
                limitLeftClear();
            }
        } else {
            limitValue.innerText = `Wybierz kategorię i datę.`;
            limitLeftClear();
        }
    } else {
        limitInfo.innerText = 'Wybierz kategorię.';
        limitValue.innerText = `Wybierz kategorię i datę.`;
        limitLeftClear();
    }
}

// Event listeners
categoryField.addEventListener('change', async () => {
    const category = categoryField.options[categoryField.selectedIndex].value;
    const date = dateField.value;
    const amount = amountField.value;

    eventsAction(category, date, amount);
})

dateField.addEventListener('change', async () => {
    const category = categoryField.options[categoryField.selectedIndex].value;
    const date = dateField.value;
    const amount = amountField.value;

    eventsAction(category, date, amount);
})

amountField.addEventListener('input', async () => {
    const category = categoryField.options[categoryField.selectedIndex].value;
    const date = dateField.value;
    const amount = amountField.value;

    eventsAction(category, date, amount);
})
