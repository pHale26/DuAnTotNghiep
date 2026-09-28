<?php
$path = "resources/views/frontend/tours/checkout.blade.php";
$content = file_get_contents($path);

$js = <<<HTML
    function checkEmailMatch() {
        const email = document.getElementById("customer_email")?.value;
        const confirm = document.getElementById("customer_email_confirmation");
        const error = document.getElementById("email_match_error");
        
        if (confirm && error) {
            if (confirm.value && email !== confirm.value) {
                confirm.classList.add("is-invalid");
                error.style.display = "block";
                confirm.setCustomValidity("Email nhập lại không khớp!");
            } else {
                confirm.classList.remove("is-invalid");
                error.style.display = "none";
                confirm.setCustomValidity("");
            }
        }
    }
    
    document.getElementById("customer_email")?.addEventListener("input", checkEmailMatch);
</script>
HTML;

$content = str_replace("</script>", $js, $content);
file_put_contents($path, $content);

