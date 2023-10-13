const amountArea = document.querySelector('#amount');
const dateArea = document.querySelector('#date');
const categoryArea = document.querySelector('#category');

const limitCategory = document.querySelector('#limit-category');
const limitDate = document.querySelector('#limit-date');
const limitAmount = document.querySelector('#limit-amount');

const areaCategory = limitData => {
    const limit = limitData.cash_limit;

    if (limit > 0) {
        limitCategory.innerText = `Ustaliłeś limit ${limit} PLN dla danej kategorii.`;
    } else {
        limitCategory.innerText = 'Limit nie został ustalony.';
    }   
}

const areaDate = expenseOfMonth => {
    limitDate.innerText = !!expenseOfMonth ? `Wydałeś ${expenseOfMonth} PLN w tym miesiącu na daną kategorię.` : `Nie wydałeś jeszcze żadnej kwoty w tej kategorii!`;
}

const areaAmount = (limitData, expenseOfMonth, amount) => {
    const limit = limitData.cash_limit;
    const isLimitActive = limitData.is_limit_active;

    if (isLimitActive) {
        limitAmount.innerText = `Limit: ${(limit - expenseOfMonth - amount)} PLN.`;

        (limit - expenseOfMonth - amount) < 0 ? limitAmount.classList.add('above-limit') : limitAmount.classList.remove('above-limit');
    }
}

const limitAmountClear = () => {
    limitAmount.classList.remove('above-limit');
    limitAmount.innerText = 'Limit nie został ustalony lub nie wpisano kwoty wydatku.';
}

const getLimitForCategory = async (category) => {
    try {
        const res = await fetch(`../api/limit/${category}`);
        const data = await res.json();
        return data;
    } catch (e) {
        console.log('ERROR', e);
    }
}

const getExpenseOfMonth = async (category, date) => {
    try {
        const res = await fetch(`../api/limitSum/${category}/${date}`);
        const data = await res.json();
        return data;
    } catch (e) {
        console.log('ERROR', e);
    }
}

const action = async (category, date, amount) => {
    if (!!category) {
        const limitData = await getLimitForCategory(category);
        areaCategory(limitData);

        if (!!date) {
            const expenseOfMonth = await getExpenseOfMonth(category, date);
            areaDate(expenseOfMonth);

            if (!!amount && !!limitData) {
                areaAmount(limitData, expenseOfMonth, amount);
            } else {
                limitAmountClear();
            }
        } else {
            limitDate.innerText = `Wybierz kategorię i datę.`;
            limitAmountClear();
        }
    } else {
        limitCategory.innerText = 'Wybierz kategorię.';
        limitDate.innerText = `Wybierz kategorię i datę.`;
        limitAmountClear();
    }
}

categoryArea.addEventListener('change', async () => {
    const category = categoryArea.options[categoryArea.selectedIndex].value;
    const date = dateArea.value;
    const amount = amountArea.value;

    action(category, date, amount);
})

dateArea.addEventListener('change', async () => {
    const category = categoryArea.options[categoryArea.selectedIndex].value;
    const date = dateArea.value;
    const amount = amountArea.value;

    action(category, date, amount);
})

amountArea.addEventListener('input', async () => {
    const category = categoryArea.options[categoryArea.selectedIndex].value;
    const date = dateArea.value;
    const amount = amountArea.value;

    action(category, date, amount);
})
