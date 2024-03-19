const footerFaq = document.querySelectorAll(".faq__box");
footerFaq.forEach((faq) => {
    faq.addEventListener("click", () => {
        faq.classList.toggle("active");
    });
});