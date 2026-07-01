const tabButtons = document.querySelectorAll(".td-tabs button");
const tabPanels = document.querySelectorAll(".tab-panel");

tabButtons.forEach(button => {
   button.addEventListener("click", () => {
      const tabId = button.dataset.tab;

      tabButtons.forEach(btn => btn.classList.remove("active"));
      tabPanels.forEach(panel => panel.classList.remove("active"));

      button.classList.add("active");
      document.getElementById(tabId).classList.add("active");
   });
});