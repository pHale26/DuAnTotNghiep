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
                    document.getElementById("email_suggestion_banner").classList.add("d-none");
                    emailInput.setCustomValidity(""); // Reset
                    return;
                }
                
                // Block typos logic
                const domains = ["gmail.com", "yahoo.com", "hotmail.com", "outlook.com", "icloud.com"];
                const parts = email.split("@");
                if (parts.length === 2) {
                    const domain = parts[1].toLowerCase();
                    let suggestion = null;
                    // Strict block for bad domains
                    const blockedDomains = ["gmial.com", "gmal.com", "gamil.com", "gmail.con", "yaho.com", "yahoo.con", "hotmal.com"];
                    if (blockedDomains.includes(domain)) {
                        emailInput.setCustomValidity("Tên miền email không hợp lệ. Có phải ý bạn là " + domain.replace(/gmial|gmal|gamil/, "gmail").replace(".con", ".com").replace("yaho", "yahoo").replace("hotmal", "hotmail") + "?");
                        // We also show suggestion banner
                        if (domain.includes("gm")) suggestion = "gmail.com";
                        else if (domain.includes("yah")) suggestion = "yahoo.com";
                        else if (domain.includes("hot")) suggestion = "hotmail.com";
                    } else {
                        emailInput.setCustomValidity("");
                    }
                    
                    const suggBanner = document.getElementById("email_suggestion_banner");
                    if (suggestion) {
                        const fullSugg = parts[0] + "@" + suggestion;
                        document.getElementById("email_suggestion_text").innerText = fullSugg;
                        suggBanner.classList.remove("d-none");
                        document.getElementById("email_suggestion_btn").onclick = function(e) {
                            e.preventDefault();
                            emailInput.value = fullSugg;
                            suggBanner.classList.add("d-none");
                            emailInput.setCustomValidity("");
                            emailInput.dispatchEvent(new Event("input"));
                        };
                    } else {
                        suggBanner.classList.add("d-none");
                    }
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

$content = str_replace("</style>\n@endsection", "</style>\n" . $js . "\n@endsection", $content);
file_put_contents($path, $content);

