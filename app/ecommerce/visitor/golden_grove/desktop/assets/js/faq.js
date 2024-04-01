const faq = document.querySelectorAll(".faq");
faq.forEach((faqs) => {
    faqs.addEventListener("click", () => {
        faqs.classList.toggle("active");
    });
});