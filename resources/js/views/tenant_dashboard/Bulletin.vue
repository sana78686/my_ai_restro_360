<template>
    <div class="settings-page">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">{{ $t("tenant_dashboard.bulletin.title") }}</h4>
        </div>

        <div class="card">
            <div class="card-body">
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{
                            $t("dashboard_common.loading")
                        }}</span>
                    </div>
                </div>
                <form
                    v-else
                    @submit.prevent="saveSettings"
                    class="settings-form"
                >
                    <div class="section mb-4">
                        <h5 class="section-title">
                            {{ $t("tenant_dashboard.bulletin.discountNews") }}
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label"
                                    >{{
                                        $t("tenant_dashboard.bulletin.discount")
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.bulletin.discountTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.discount"
                                    type="number"
                                    min="0"
                                    max="100"
                                    class="form-control"
                                    required
                                    :placeholder="
                                        $t(
                                            'tenant_dashboard.bulletin.discountPlaceholder'
                                        )
                                    "
                                />
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button
                            type="submit"
                            class="btn btn-primary"
                            :disabled="saving"
                        >
                            <span
                                v-if="saving"
                                class="spinner-border spinner-border-sm me-2"
                                role="status"
                            ></span>
                            {{ $t("tenant_dashboard.bulletin.saveDiscount") }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, watch } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import { Tooltip } from "bootstrap";

export default {
    name: "Settings",
    setup() {
        const loading = ref(true);
        const saving = ref(false);
        const logoInput = ref(null);
        const staticCurrencies = [
            { name: "USD", symbol: "$" },
            { name: "EUR", symbol: "€" },
            { name: "GBP", symbol: "£" },
            { name: "INR", symbol: "₹" },
            { name: "CAD", symbol: "C$" },
            { name: "AUD", symbol: "A$" },
            { name: "JPY", symbol: "¥" },
            // Add more as needed
        ];
        const form = ref({
            discount: "",
        });

        const handleLogoUpload = async (event) => {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file size (2MB max)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Logo file size must be less than 2MB",
                });
                return;
            }

            // Validate file type
            if (!file.type.startsWith("image/")) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Please upload an image file",
                });
                return;
            }

            const formData = new FormData();
            formData.append("logo", file);

            try {
                const response = await axios.post(
                    "/tenant/upload-logo",
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                );

                if (response.data.success) {
                    // prefer logo_url (signed/public S3 URL) if backend returns it
                    form.value.logo =
                        response.data.logo_url || response.data.url || "";
                } else {
                    throw new Error(
                        response.data.message || "Failed to upload logo"
                    );
                }
            } catch (error) {
                console.error("Error uploading logo:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text:
                        error.response?.data?.message ||
                        "Failed to upload logo",
                });
            }
        };

        const fetchSettings = async () => {
            try {
                const response = await axios.get("/tenant/settings");
                if (response.data.success && response.data.data) {
                    const settings = response.data.data;
                    form.value = {
                        discount: settings.discount || "",
                    };
                    // --- Fix: set currency by symbol if present ---
                    if (settings.currency_symbol) {
                        const found = staticCurrencies.find(
                            (c) => c.symbol === settings.currency_symbol
                        );
                        if (found) {
                            form.value.currency_symbol = found.symbol;
                        }
                    }
                }
            } catch (error) {
                console.error("Error fetching settings:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to fetch settings",
                });
            } finally {
                loading.value = false;
            }
        };

        const saveSettings = async () => {
            saving.value = true;
            try {
                const response = await axios.put("/tenant/settings/discount", {
                    discount: form.value.discount,
                });
                if (response.data.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: "Discount updated successfully",
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                    });
                } else {
                    throw new Error(
                        response.data.message || "Failed to save discount"
                    );
                }
            } catch (error) {
                console.error("Error saving discount:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text:
                        error.response?.data?.message ||
                        "Failed to save discount",
                });
            } finally {
                saving.value = false;
            }
        };

        onMounted(() => {
            Promise.all([fetchSettings()]).then(() => {
                // setCurrencySymbol() // This function is no longer needed
            });
            document
                .querySelectorAll('[data-bs-toggle="tooltip"]')
                .forEach((el) => {
                    new Tooltip(el);
                });
        });

        return {
            form,
            loading,
            saving,
            logoInput,
            staticCurrencies,
            handleLogoUpload,
            saveSettings,
        };
    },
};
</script>

<style scoped>
.settings-page {
    padding: 20px;
    font-family: "Inter", "Roboto", "Segoe UI", Arial, sans-serif;
}

.section {
    background: #fff;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.section-title {
    color: #2c3e50;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #f0f0f0;
}

.form-label {
    font-weight: 500;
    color: #4a5568;
    margin-bottom: 0.5rem;
}

.form-control,
.form-select {
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 0.5rem 0.75rem;
    font-size: 0.95rem;
}

.form-control:focus,
.form-select:focus {
    border-color: #4299e1;
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

.btn-primary {
    background: #4299e1;
    border: none;
    padding: 0.6rem 1.5rem;
    font-weight: 500;
    border-radius: 6px;
    transition: all 0.2s;
}

.btn-primary:hover {
    background: #3182ce;
    transform: translateY(-1px);
}

.btn-primary:disabled {
    background: #a0aec0;
    cursor: not-allowed;
    transform: none;
}

.form-check-input:checked {
    background-color: #4299e1;
    border-color: #4299e1;
}

.logo-preview {
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
    border-radius: 6px;
    overflow: hidden;
}

.logo-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}
</style>
