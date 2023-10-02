<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use App\Models\Category;
use App\Models\Payform;

class Settings extends Authenticated
{

    protected function before()
    {
        parent::before();
        $this->user = Auth::getUser();
        $this->income_categories = Category::getNameCategoryIncome($this->user->id);
        $this->expense_categories = Category::getNameCategoryExpense($this->user->id);
        $this->payment_categories = Payform::getNamePayform($this->user->id);
    }

    protected function after()
    {
    }

    public function showAction()
    {
        View::renderTemplate('Settings/show.html', [
            'user' => $this->user,
            'income_categories' => $this->income_categories,
            'expense_categories' => $this->expense_categories,
            'payment_categories' => $this->payment_categories
        ]);
    }

    public function addIncomeCategoryAction()
    {
        $userId = $this->user->id;
        $incomeCategory = $_POST['new-category-name'];

        echo json_encode(Category::addIncomeCategory($userId, $incomeCategory), JSON_UNESCAPED_UNICODE);
    }

    public function addExpenseCategoryAction()
    {
        $userId = $this->user->id;
        $expenseCategory = $_POST['new-category-name'];

        echo json_encode(Category::addExpenseCategory($userId, $expenseCategory), JSON_UNESCAPED_UNICODE);
    }

    public function addPaymentCategoryAction()
    {
        $userId = $this->user->id;
        $paymentMethod = $_POST['new-category-name'];

        echo json_encode(Payform::addPaymentCategory($userId, $paymentMethod), JSON_UNESCAPED_UNICODE);
    }

    public function editIncomeCategoryAction()
    {
        $userId = $this->user->id;
        $categoryId = $this->route_params['id'];
        $newCategoryName = $_POST['new-category-name'];

        echo json_encode(Category::editIncomeCategory($userId, $categoryId, $newCategoryName), JSON_UNESCAPED_UNICODE);
    }

    public function editExpenseCategoryAction()
    {
        $userId = $this->user->id;
        $categoryId = $this->route_params['id'];
        $newCategoryName = $_POST['new-category-name'];

        echo json_encode(Category::editExpenseCategory($userId, $categoryId, $newCategoryName), JSON_UNESCAPED_UNICODE);
    }

    public function editPaymentCategoryAction()
    {
        $userId = $this->user->id;
        $categoryId = $this->route_params['id'];
        $newCategoryName = $_POST['new-category-name'];

        echo json_encode(Payform::editPaymentCategory($userId, $categoryId, $newCategoryName), JSON_UNESCAPED_UNICODE);
    }

    public function checkRemoveIncomeCategoryAction()
    {
        $userId = $this->user->id;
        $categoryId = $this->route_params['id'];

        echo json_encode(Category::checkRemoveIncomeCategory($userId, $categoryId), JSON_UNESCAPED_UNICODE);
    }

    public function checkRemoveExpenseCategoryAction()
    {
        $userId = $this->user->id;
        $categoryId = $this->route_params['id'];

        echo json_encode(Category::checkRemoveExpenseCategory($userId, $categoryId), JSON_UNESCAPED_UNICODE);
    }

    public function checkRemovePaymentCategoryAction()
    {
        $userId = $this->user->id;
        $categoryId = $this->route_params['id'];

        echo json_encode(Payform::checkRemovePaymentCategory($userId, $categoryId), JSON_UNESCAPED_UNICODE);
    }

    public function removeIncomeCategoryAction()
    {
        $userId = $this->user->id;
        $categoryId = $this->route_params['id'];

        echo json_encode(Category::removeIncomeCategory($userId, $categoryId), JSON_UNESCAPED_UNICODE);
    }

    public function removeExpenseCategoryAction()
    {
        $userId = $this->user->id;
        $categoryId = $this->route_params['id'];

        echo json_encode(Category::removeExpenseCategory($userId, $categoryId), JSON_UNESCAPED_UNICODE);
    }

    public function removePaymentCategoryAction()
    {
        $userId = $this->user->id;
        $categoryId = $this->route_params['id'];

        echo json_encode(Payform::removePaymentCategory($userId, $categoryId), JSON_UNESCAPED_UNICODE);
    }
}
