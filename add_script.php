<?php
$path = "resources/views/frontend/tours/checkout.blade.php";
$content = file_get_contents($path);
$js = <<<HTML
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const emailInput = document.getElementById("customer_email");
        const banner = document.getElementById("email_exists_banner");
        let timeout = null;
        
        if (emailInput && banner) {
            emailInput.addEventListener("input", function() {
                clearTimeout(timeout);
                const email = this.value.trim();
                
                if (!email || !email.includes("@") || !email.includes(".")) {
                    banner.classList.add("d-none");
                    return;
                }
                
                timeout = setTimeout(() => {
                    fetch(`/api/check-email?email=\${encodeURIComponent(email)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists && !"{{ \$user ? \"logged_in\" : \"\" }}") {
                                banner.classList.remove("d-none");
                            } else {
                                banner.classList.add("d-none");
                            }
                        })
                        .catch(err => console.error(err));
                }, 500);
            });
        }
    });
</script>
HTML;
$content = str_replace("@endpush", $js . "\n@endpush", $content);
file_put_contents($path, $content);

