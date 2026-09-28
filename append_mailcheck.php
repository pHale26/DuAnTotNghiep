<?php
$path = "resources/views/frontend/tours/checkout.blade.php";
$content = file_get_contents($path);

$old_script = <<<HTML
                if (!email || !email.includes("@") || !email.includes(".")) {
                    banner.classList.add("d-none");
                    return;
                }
HTML;

$new_script = <<<HTML
                if (!email || !email.includes("@") || !email.includes(".")) {
                    banner.classList.add("d-none");
                    document.getElementById("email_suggestion_banner").classList.add("d-none");
                    return;
                }
                
                // Mailcheck suggestion logic (simple)
                const domains = ["gmail.com", "yahoo.com", "hotmail.com", "outlook.com", "icloud.com"];
                const parts = email.split("@");
                if (parts.length === 2) {
                    const domain = parts[1].toLowerCase();
                    let suggestion = null;
                    // Find closest domain if typo
                    domains.forEach(d => {
                        if (d !== domain && d.length === domain.length && d.charAt(0) === domain.charAt(0)) {
                            // extremely simple heuristic for typos like gmial.com
                            let diff = 0;
                            for (let i = 0; i < d.length; i++) {
                                if (d[i] !== domain[i]) diff++;
                            }
                            if (diff <= 2) suggestion = d;
                        }
                    });
                    
                    const suggBanner = document.getElementById("email_suggestion_banner");
                    if (suggestion) {
                        const fullSugg = parts[0] + "@" + suggestion;
                        document.getElementById("email_suggestion_text").innerText = fullSugg;
                        suggBanner.classList.remove("d-none");
                        document.getElementById("email_suggestion_btn").onclick = function(e) {
                            e.preventDefault();
                            emailInput.value = fullSugg;
                            suggBanner.classList.add("d-none");
                            emailInput.dispatchEvent(new Event("input"));
                        };
                    } else {
                        suggBanner.classList.add("d-none");
                    }
                }
HTML;

$content = str_replace($old_script, $new_script, $content);
file_put_contents($path, $content);

