const footerFaq = document.querySelectorAll(".footer_faq");
footerFaq.forEach((faq) => {
    faq.addEventListener("click", () => {
        faq.classList.toggle("active");
    });
});