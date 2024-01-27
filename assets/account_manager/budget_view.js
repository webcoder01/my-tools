function hideBudgetEdition(editionWrapper) {
  editionWrapper.classList.remove('flex');
  editionWrapper.classList.add('hidden');
}

function showBudgetEdition(editionWrapper) {
  editionWrapper.classList.remove('hidden');
  editionWrapper.classList.add('flex');
}

function hideEditionOfGivenBudgets(editionWrapperOfBudgets) {
  for (const wrapper of editionWrapperOfBudgets) {
    hideBudgetEdition(wrapper);
  }
}

function hideEditionOfAllBudgets() {
  const editionWrappers = document.querySelectorAll('.budget-edition');
  hideEditionOfGivenBudgets(editionWrappers);
}

function hideBudgetEditionOnCategoryHide() {
  const categories = document.querySelectorAll('summary');

  for (const category of categories) {
    category.addEventListener('click', function(event) {
      const categoryClicked = event.currentTarget;
      const isCategoryClosing = categoryClicked.parentElement.hasAttribute('open');

      if (isCategoryClosing) {
        const editionOfBudgetsFromCategory = categoryClicked.parentElement.querySelectorAll('.budget-edition.flex');
        hideEditionOfGivenBudgets(editionOfBudgetsFromCategory);
      }
    })
  }
}

function toggleBudgetEditionOnBudgetClick() {
  const budgets = document.querySelectorAll('.budget-widget');

  for (const budget of budgets) {
    budget.addEventListener('click', function(event) {
      const budgetClicked = event.currentTarget;
      const editionWrapper = budgetClicked.querySelector('.budget-edition');

      if (editionWrapper === null) {
        return;
      }

      if (editionWrapper.classList.contains('hidden')) {
        hideEditionOfAllBudgets();
        showBudgetEdition(editionWrapper);
      } else {
        hideBudgetEdition(editionWrapper);
      }
    });
  }
}

document.addEventListener('DOMContentLoaded', function () {
  hideBudgetEditionOnCategoryHide();
  toggleBudgetEditionOnBudgetClick();
});
