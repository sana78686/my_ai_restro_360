<template>
    <div class="settings-page">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">{{ $t("tenant_dashboard.settings.title") }}</h4>
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
                    <!-- Basic Information -->
                    <div class="section mb-4">
                        <h5 class="section-title">
                            {{
                                $t("tenant_dashboard.settings.basicInformation")
                            }}
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label"
                                    >{{
                                        $t(
                                            "tenant_dashboard.settings.businessName"
                                        )
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.businessNameTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.business_name"
                                    type="text"
                                    class="form-control"
                                    required
                                />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"
                                    >{{ $t("tenant_dashboard.settings.logo") }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.logoTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <div class="d-flex align-items-center gap-3">
                                    <div v-if="form.logo" class="logo-preview">
                                        <img
                                            :src="form.logo"
                                            alt="Restaurant Logo"
                                            class="img-thumbnail"
                                            style="max-height: 100px"
                                        />
                                    </div>
                                    <div class="flex-grow-1">
                                        <input
                                            type="file"
                                            class="form-control"
                                            accept="image/*"
                                            @change="handleLogoUpload"
                                            ref="logoInput"
                                        />
                                        <small class="text-muted">{{
                                            $t(
                                                "tenant_dashboard.settings.logoRequirements"
                                            )
                                        }}</small>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-md-6">
                                <label class="form-label"
                                    >{{ $t("tenant_dashboard.settings.email") }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.emailTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="form-control"
                                    required
                                    disabled
                                    readonly
                                />
                            </div> -->
                            <!-- <div class="col-md-6">
                                <label class="form-label"
                                    >{{ $t("tenant_dashboard.settings.phone") }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.phoneTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    class="form-control"
                                    required
                                />
                            </div> -->
                            <!-- restaurent mail -->
                            <div class="col-md-6">
                                <label class="form-label"
                                    >{{ $t("tenant_dashboard.settings.publicemail") }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.emailTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.public_email"
                                    type="email"
                                    class="form-control"
                                    required
                                />
                            </div>
                            <!-- restaurent phone -->
                             <div class="col-md-6">
                                <label class="form-label"
                                    >{{ $t("tenant_dashboard.settings.publicphone") }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.phoneTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.public_phone"
                                    type="tel"
                                    class="form-control"
                                    required
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Location Information -->
                    <div class="section mb-4">
                        <h5 class="section-title">
                            {{
                                $t(
                                    "tenant_dashboard.settings.locationInformation"
                                )
                            }}
                        </h5>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label"
                                    >{{
                                        $t("tenant_dashboard.settings.address")
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.addressTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.address"
                                    type="text"
                                    class="form-control"
                                    required
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"
                                    >{{ $t("tenant_dashboard.settings.city") }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.cityTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.city"
                                    type="text"
                                    class="form-control"
                                    required
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"
                                    >{{ $t("tenant_dashboard.settings.state") }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.stateTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.state"
                                    type="text"
                                    class="form-control"
                                    required
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"
                                    >{{
                                        $t(
                                            "tenant_dashboard.settings.postalCode"
                                        )
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.postalCodeTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.postal_code"
                                    type="text"
                                    class="form-control"
                                    required
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"
                                    >{{
                                        $t("tenant_dashboard.settings.country")
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.countryTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.country"
                                    type="text"
                                    class="form-control"
                                    required
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"
                                    >{{
                                        $t("tenant_dashboard.settings.placeId")
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.placeIdTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.place_id"
                                    type="text"
                                    class="form-control"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"
                                    >{{
                                        $t(
                                            "tenant_dashboard.settings.pickupTimeRange"
                                        )
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.pickupTimeRangeTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.pickup_start_end_time"
                                    type="text"
                                    class="form-control"
                                    placeholder="e.g., 9:00 AM - 10:00 PM"
                                />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"
                                    >{{
                                        $t("tenant_dashboard.settings.latitude")
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.latitudeTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.latitude"
                                    type="number"
                                    step="any"
                                    class="form-control"
                                />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"
                                    >{{
                                        $t(
                                            "tenant_dashboard.settings.longitude"
                                        )
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.longitudeTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.longitude"
                                    type="number"
                                    step="any"
                                    class="form-control"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- System Settings -->
                    <div class="section mb-4">
                        <h5 class="section-title">
                            {{ $t("tenant_dashboard.settings.systemSettings") }}
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label"
                                    >{{
                                        $t("tenant_dashboard.settings.currency")
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.currencyTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <div class="input-group">
                                    <select
                                        v-model="form.currency_symbol"
                                        class="form-select"
                                    >
                                        <option
                                            v-for="c in staticCurrencies"
                                            :key="c.symbol"
                                            :value="c.symbol"
                                        >
                                            {{ c.name }} ({{ c.symbol }})
                                        </option>
                                    </select>
                                    <span
                                        class="input-group-text"
                                        v-if="form.currency_symbol"
                                        >{{ form.currency_symbol }}</span
                                    >
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"
                                    >{{
                                        $t("tenant_dashboard.settings.timezone")
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.timezoneTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <select
                                    v-model="form.timezone"
                                    class="form-select"
                                >
                                    <option value="UTC">
                                        {{
                                            $t(
                                                "tenant_dashboard.settings.timezoneUTC"
                                            )
                                        }}
                                    </option>
                                    <option value="America/New_York">
                                        {{
                                            $t(
                                                "tenant_dashboard.settings.timezoneEastern"
                                            )
                                        }}
                                    </option>
                                    <option value="America/Chicago">
                                        {{
                                            $t(
                                                "tenant_dashboard.settings.timezoneCentral"
                                            )
                                        }}
                                    </option>
                                    <option value="America/Denver">
                                        {{
                                            $t(
                                                "tenant_dashboard.settings.timezoneMountain"
                                            )
                                        }}
                                    </option>
                                    <option value="America/Los_Angeles">
                                        {{
                                            $t(
                                                "tenant_dashboard.settings.timezonePacific"
                                            )
                                        }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"
                                    >{{
                                        $t(
                                            "tenant_dashboard.settings.dateFormat"
                                        )
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.dateFormatTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <select
                                    v-model="form.date_format"
                                    class="form-select"
                                >
                                    <option value="Y-m-d">
                                        {{
                                            $t(
                                                "tenant_dashboard.settings.dateFormatYMD"
                                            )
                                        }}
                                    </option>
                                    <option value="m/d/Y">
                                        {{
                                            $t(
                                                "tenant_dashboard.settings.dateFormatMDY"
                                            )
                                        }}
                                    </option>
                                    <option value="d/m/Y">
                                        {{
                                            $t(
                                                "tenant_dashboard.settings.dateFormatDMY"
                                            )
                                        }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"
                                    >{{
                                        $t(
                                            "tenant_dashboard.settings.timeFormat"
                                        )
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.timeFormatTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <select
                                    v-model="form.time_format"
                                    class="form-select"
                                >
                                    <option value="H:i:s">
                                        {{
                                            $t(
                                                "tenant_dashboard.settings.timeFormat24"
                                            )
                                        }}
                                    </option>
                                    <option value="h:i:s A">
                                        {{
                                            $t(
                                                "tenant_dashboard.settings.timeFormat12"
                                            )
                                        }}
                                    </option>
                                </select>
                            </div>

                            <!-- Delivery Range Configuration -->
                            <div class="col-12">
                                <h6 class="mb-3">
                                    {{
                                        $t(
                                            "tenant_dashboard.settings.deliveryRange"
                                        )
                                    }}
                                </h6>
                                
                                <!-- Current Delivery Range Display -->
                                <div v-if="hasExistingDeliveryRange" class="mb-4">
                                    <div class="alert alert-info">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-history me-2"></i>
                                                <strong>Current Delivery Range:</strong>
                                                <span class="ms-2 badge bg-primary fs-6">
                                                    {{ currentDeliveryRangeDisplay }}
                                                </span>
                                            </div>
                                            <div>
                                                <small class="text-muted me-3">
                                                    Last updated: {{ lastUpdated }}
                                                </small>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    @click="clearDeliveryRange"
                                                >
                                                    <i class="fas fa-times me-1"></i>
                                                    Clear Range
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label"
                                            >{{
                                                $t(
                                                    "tenant_dashboard.settings.deliveryRangeNortheast"
                                                )
                                            }}
                                            <span
                                                class="ms-1"
                                                data-bs-toggle="tooltip"
                                                :title="
                                                    $t(
                                                        'tenant_dashboard.settings.deliveryRangeNortheastTooltip'
                                                    )
                                                "
                                            >
                                                <i
                                                    class="fas fa-info-circle text-secondary"
                                                ></i>
                                            </span>
                                        </label>
                                        <div class="position-relative">
                                            <input
                                                v-model="deliveryRangeNortheast"
                                                type="text"
                                                class="form-control"
                                                placeholder="Click on map to set Northeast corner"
                                                readonly
                                                @click="showMapModal = true"
                                            />
                                            <button
                                                type="button"
                                                class="btn btn-outline-primary btn-sm position-absolute end-0 top-50 translate-middle-y me-2"
                                                @click="showMapModal = true"
                                            >
                                                <i
                                                    class="fas fa-map-marker-alt"
                                                ></i>
                                                Set on Map
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"
                                            >{{
                                                $t(
                                                    "tenant_dashboard.settings.deliveryRangeSouthwest"
                                                )
                                            }}
                                            <span
                                                class="ms-1"
                                                data-bs-toggle="tooltip"
                                                :title="
                                                    $t(
                                                        'tenant_dashboard.settings.deliveryRangeSouthwestTooltip'
                                                    )
                                                "
                                            >
                                                <i
                                                    class="fas fa-info-circle text-secondary"
                                                ></i>
                                            </span>
                                        </label>
                                        <div class="position-relative">
                                            <input
                                                v-model="deliveryRangeSouthwest"
                                                type="text"
                                                class="form-control"
                                                placeholder="Click on map to set Southwest corner"
                                                readonly
                                                @click="showMapModal = true"
                                            />
                                            <button
                                                type="button"
                                                class="btn btn-outline-primary btn-sm position-absolute end-0 top-50 translate-middle-y me-2"
                                                @click="showMapModal = true"
                                            >
                                                <i
                                                    class="fas fa-map-marker-alt"
                                                ></i>
                                                Set on Map
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Delivery Range Status -->
                                <div
                                    v-if="
                                        deliveryRangeNortheast &&
                                        deliveryRangeSouthwest
                                    "
                                    class="mt-3"
                                >
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <strong>New delivery area configured:</strong><br />
                                        <small>
                                            Northeast: {{ deliveryRangeNortheast }}<br />
                                            Southwest: {{ deliveryRangeSouthwest }}
                                        </small>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Click "Save Settings" to update your delivery range
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-else-if="form.latitude && form.longitude && !hasExistingDeliveryRange"
                                    class="mt-3"
                                >
                                    <div class="alert alert-light">
                                        <i
                                            class="fas fa-map-marker-alt me-2 text-primary"
                                        ></i>
                                        <strong>Restaurant Location Set:</strong>
                                        {{ form.address || "Location configured" }}<br />
                                        <small class="text-muted"
                                            >Click "Set on Map" to configure your delivery area</small
                                        >
                                    </div>
                                </div>
                                <div v-else-if="!form.latitude || !form.longitude" class="mt-3">
                                    <div class="alert alert-warning">
                                        <i
                                            class="fas fa-exclamation-triangle me-2"
                                        ></i>
                                        <strong>Restaurant location required:</strong>
                                        Please set your restaurant's address and location first to configure delivery areas.
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"
                                    >{{
                                        $t("tenant_dashboard.settings.status")
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.statusTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <div class="form-check form-switch mt-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        v-model="form.is_active"
                                        id="statusSwitch"
                                    />
                                    <label
                                        class="form-check-label"
                                        for="statusSwitch"
                                    >
                                        {{
                                            form.is_active
                                                ? $t(
                                                      "tenant_dashboard.settings.statusActive"
                                                  )
                                                : $t(
                                                      "tenant_dashboard.settings.statusInactive"
                                                  )
                                        }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Links -->
                    <div class="section mb-4">
                        <h5 class="section-title">
                            {{
                                $t("tenant_dashboard.settings.socialMediaLinks")
                            }}
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label"
                                    >{{
                                        $t(
                                            "tenant_dashboard.settings.facebookLink"
                                        )
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.facebookLinkTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.facebook_link"
                                    type="url"
                                    class="form-control"
                                />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"
                                    >{{
                                        $t(
                                            "tenant_dashboard.settings.twitterLink"
                                        )
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.twitterLinkTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.twitter_link"
                                    type="url"
                                    class="form-control"
                                />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"
                                    >{{
                                        $t(
                                            "tenant_dashboard.settings.linkedinLink"
                                        )
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.linkedinLinkTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.linkedin_link"
                                    type="url"
                                    class="form-control"
                                />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"
                                    >{{
                                        $t(
                                            "tenant_dashboard.settings.googlePlusLink"
                                        )
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.googlePlusLinkTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.google_plus_link"
                                    type="url"
                                    class="form-control"
                                />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"
                                    >{{
                                        $t(
                                            "tenant_dashboard.settings.instagramLink"
                                        )
                                    }}
                                    <span
                                        class="ms-1"
                                        data-bs-toggle="tooltip"
                                        :title="
                                            $t(
                                                'tenant_dashboard.settings.instagramLinkTooltip'
                                            )
                                        "
                                    >
                                        <i
                                            class="fas fa-info-circle text-secondary"
                                        ></i>
                                    </span>
                                </label>
                                <input
                                    v-model="form.instagram_link"
                                    type="url"
                                    class="form-control"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="section mb-4">
                        <h5 class="section-title">
                            {{ $t("tenant_dashboard.settings.description") }}
                        </h5>
                        <label class="form-label"
                            >{{
                                $t("tenant_dashboard.settings.descriptionLabel")
                            }}
                            <span
                                class="ms-1"
                                data-bs-toggle="tooltip"
                                :title="
                                    $t(
                                        'tenant_dashboard.settings.descriptionTooltip'
                                    )
                                "
                            >
                                <i
                                    class="fas fa-info-circle text-secondary"
                                ></i>
                            </span>
                        </label>
                        <textarea
                            v-model="form.description"
                            class="form-control"
                            rows="3"
                        ></textarea>
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
                            {{ $t("tenant_dashboard.settings.saveSettings") }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Map Modal for Delivery Range Selection -->
    <div
        v-if="showMapModal"
        class="modal fade show d-block"
        tabindex="-1"
        style="background-color: rgba(0, 0, 0, 0.5)"
    >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Set Delivery Range 
                        <span v-if="hasExistingDeliveryRange" class="badge bg-info ms-2">
                            Current: {{ currentDeliveryRangeDisplay }}
                        </span>
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        @click="closeMapModal"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label"
                                    >Select Range Type:</label
                                >
                                <div class="btn-group w-100" role="group">
                                    <input
                                        type="radio"
                                        class="btn-check"
                                        id="northeast"
                                        v-model="selectedRangeType"
                                        value="northeast"
                                    />
                                    <label
                                        class="btn btn-outline-primary"
                                        for="northeast"
                                        >Northeast Corner</label
                                    >

                                    <input
                                        type="radio"
                                        class="btn-check"
                                        id="southwest"
                                        v-model="selectedRangeType"
                                        value="southwest"
                                    />
                                    <label
                                        class="btn btn-outline-primary"
                                        for="southwest"
                                        >Southwest Corner</label
                                    >
                                </div>
                            </div>
                            <div
                                id="delivery-range-map"
                                style="height: 400px; width: 100%"
                            ></div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Click on the map to set the
                                    {{
                                        selectedRangeType === "northeast"
                                            ? "Northeast"
                                            : "Southwest"
                                    }}
                                    corner of your delivery area.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="delivery-suggestions">
                                <h6 class="mb-3">
                                    <i class="fas fa-map-marked-alt me-2"></i>
                                    Quick Suggestions
                                </h6>
                                <div class="suggestion-buttons">
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary btn-sm w-100 mb-2"
                                        @click="setSuggestedRange('1km')"
                                    >
                                        <i
                                            class="fas fa-circle me-1"
                                            style="font-size: 0.5rem"
                                        ></i>
                                        1 km radius
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary btn-sm w-100 mb-2"
                                        @click="setSuggestedRange('3km')"
                                    >
                                        <i
                                            class="fas fa-circle me-1"
                                            style="font-size: 0.7rem"
                                        ></i>
                                        3 km radius
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary btn-sm w-100 mb-2"
                                        @click="setSuggestedRange('5km')"
                                    >
                                        <i
                                            class="fas fa-circle me-1"
                                            style="font-size: 0.9rem"
                                        ></i>
                                        5 km radius
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary btn-sm w-100 mb-2"
                                        @click="setSuggestedRange('10km')"
                                    >
                                        <i
                                            class="fas fa-circle me-1"
                                            style="font-size: 1.1rem"
                                        ></i>
                                        10 km radius
                                    </button>
                                </div>

                                <div class="mt-4">
                                    <h6 class="mb-2">Current Selection:</h6>
                                    <div
                                        v-if="deliveryRangeNortheast"
                                        class="small text-success"
                                    >
                                        <i class="fas fa-map-pin me-1"></i>
                                        NE: {{ deliveryRangeNortheast }}
                                    </div>
                                    <div
                                        v-if="deliveryRangeSouthwest"
                                        class="small text-success"
                                    >
                                        <i class="fas fa-map-pin me-1"></i>
                                        SW: {{ deliveryRangeSouthwest }}
                                    </div>
                                    <div
                                        v-if="
                                            !deliveryRangeNortheast &&
                                            !deliveryRangeSouthwest
                                        "
                                        class="small text-muted"
                                    >
                                        No corners selected yet
                                    </div>
                                </div>

                                <!-- Show existing range info -->
                                <div v-if="hasExistingDeliveryRange" class="mt-3 p-2 bg-light rounded">
                                    <h6 class="mb-2">
                                        <i class="fas fa-history me-1 text-info"></i>
                                        Existing Range
                                    </h6>
                                    <div class="small text-muted">
                                        Your current delivery range covers approximately:
                                        <strong class="text-primary">{{ currentDeliveryRangeDisplay }}</strong>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button
                                        type="button"
                                        class="btn btn-outline-warning btn-sm w-100"
                                        @click="clearDeliveryRange"
                                    >
                                        <i class="fas fa-eraser me-1"></i>
                                        Clear Selection
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="closeMapModal"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="saveDeliveryRange"
                        :disabled="
                            !deliveryRangeNortheast || !deliveryRangeSouthwest
                        "
                    >
                        {{ hasExistingDeliveryRange ? 'Update' : 'Save' }} Delivery Range
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, computed, watch, nextTick } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import { Tooltip } from "bootstrap";
import { getGoogleMapsApiKey } from "../../utils/googleMapsPlacesLoader.js";

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
        ];
        
        const form = ref({
            business_name: "",
            logo: "",
            address: "",
            postal_code: "",
            country: "",
            state: "",
            city: "",
            // phone: "",
            public_phone:"",
            // email: "",
            public_email:"",
            place_id: "",
            pickup_start_end_time: "",
            latitude: null,
            longitude: null,
            currency_symbol: "",
            timezone: "UTC",
            date_format: "Y-m-d",
            time_format: "H:i:s",
            is_active: true,
            delivery_range_northeast_lat: "",
            delivery_range_northeast_lng: "",
            delivery_range_southwest_lat: "",
            delivery_range_southwest_lng: "",
            facebook_link: "",
            twitter_link: "",
            linkedin_link: "",
            google_plus_link: "",
            instagram_link: "",
            description: "",
            updated_at: "",
        });

        // Map-related data
        const showMapModal = ref(false);
        const selectedRangeType = ref("northeast");
        const map = ref(null);
        const mapMarkers = ref([]);

        // Computed properties for delivery range display
        const deliveryRangeNortheast = computed(() => {
            if (
                form.value.delivery_range_northeast_lat &&
                form.value.delivery_range_northeast_lng
            ) {
                return `${form.value.delivery_range_northeast_lat}, ${form.value.delivery_range_northeast_lng}`;
            }
            return "";
        });

        const deliveryRangeSouthwest = computed(() => {
            if (
                form.value.delivery_range_southwest_lat &&
                form.value.delivery_range_southwest_lng
            ) {
                return `${form.value.delivery_range_southwest_lat}, ${form.value.delivery_range_southwest_lng}`;
            }
            return "";
        });

        // Check if user has existing delivery range
        const hasExistingDeliveryRange = computed(() => {
            return deliveryRangeNortheast.value && deliveryRangeSouthwest.value;
        });

        // Calculate approximate range in km
        const currentDeliveryRangeDisplay = computed(() => {
            if (!hasExistingDeliveryRange.value) return "";
            
            try {
                const neLat = parseFloat(form.value.delivery_range_northeast_lat);
                const neLng = parseFloat(form.value.delivery_range_northeast_lng);
                const swLat = parseFloat(form.value.delivery_range_southwest_lat);
                const swLng = parseFloat(form.value.delivery_range_southwest_lng);
                
                if (form.value.latitude && form.value.longitude) {
                    const centerLat = parseFloat(form.value.latitude);
                    const centerLng = parseFloat(form.value.longitude);
                    
                    // Calculate distance from center to northeast corner
                    const distance = calculateDistance(centerLat, centerLng, neLat, neLng);
                    return `${Math.round(distance)} km radius`;
                }
                
                // Fallback: show coordinates
                return "Custom Area";
            } catch (error) {
                return "Custom Area";
            }
        });

        // Last updated time
        const lastUpdated = computed(() => {
            if (!form.value.updated_at) return "Never";
            try {
                return new Date(form.value.updated_at).toLocaleDateString();
            } catch {
                return "Unknown";
            }
        });

        // Helper function to calculate distance between two coordinates
        const calculateDistance = (lat1, lon1, lat2, lon2) => {
            const R = 6371; // Earth's radius in km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        };

        const handleLogoUpload = async (event) => {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Logo file size must be less than 2MB",
                });
                return;
            }

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
                    await fetchSettings();
                    if (form.value.logo)
                        form.value.logo =
                            form.value.logo.split("?")[0] + "?v=" + Date.now();
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
                        business_name: settings.business_name || "",
                        logo: settings.logo_url || settings.logo || "",
                        address: settings.address || "",
                        postal_code: settings.postal_code || "",
                        country: settings.country || "",
                        state: settings.state || "",
                        city: settings.city || "",
                        // phone: settings.phone || "",
                     //   email: settings.email || "",
                        public_email: settings.public_email || "",
                        public_phone: settings.public_phone || "",
                        place_id: settings.place_id || "",
                        pickup_start_end_time: settings.pickup_start_end_time || "",
                        latitude: settings.latitude || null,
                        longitude: settings.longitude || null,
                        currency_symbol: settings.currency_symbol || "",
                        timezone: settings.timezone || "UTC",
                        date_format: settings.date_format || "Y-m-d",
                        time_format: settings.time_format || "H:i:s",
                        is_active: !!settings.is_active,
                        delivery_range_northeast_lat: settings.delivery_range_northeast_lat || "",
                        delivery_range_northeast_lng: settings.delivery_range_northeast_lng || "",
                        delivery_range_southwest_lat: settings.delivery_range_southwest_lat || "",
                        delivery_range_southwest_lng: settings.delivery_range_southwest_lng || "",
                        facebook_link: settings.facebook_link || "",
                        twitter_link: settings.twitter_link || "",
                        linkedin_link: settings.linkedin_link || "",
                        google_plus_link: settings.google_plus_link || "",
                        instagram_link: settings.instagram_link || "",
                        description: settings.description || "",
                        updated_at: settings.updated_at || "",
                    };
                    
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
                const response = await axios.put(
                    "/tenant/settings",
                    form.value
                );
                if (response.data.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: "Settings updated successfully",
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                    });
                    // Refresh settings to get updated timestamp
                    await fetchSettings();
                } else {
                    throw new Error(
                        response.data.message || "Failed to save settings"
                    );
                }
            } catch (error) {
                console.error("Error saving settings:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text:
                        error.response?.data?.message ||
                        "Failed to save settings",
                });
            } finally {
                saving.value = false;
            }
        };

        // Map methods
        const loadGoogleMapsScript = () => {
            if (window.google && window.google.maps) {
                return Promise.resolve();
            }

            const key = getGoogleMapsApiKey();
            if (!key) {
                console.warn(
                    "[AiRestro360] Delivery map skipped: set VITE_GOOGLE_MAPS_API_KEY in .env and restart Vite."
                );
                return Promise.resolve();
            }

            return new Promise((resolve, reject) => {
                const script = document.createElement("script");
                script.src = `https://maps.google.com/maps/api/js?key=${encodeURIComponent(key)}&libraries=places`;
                script.async = true;
                script.defer = true;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        };

        const initMap = () => {
            if (!window.google || !window.google.maps) return;

            const mapElement = document.getElementById("delivery-range-map");
            if (!mapElement) return;

            let centerLat = 33.6844;
            let centerLng = 73.0479;
            let zoom = 12;

            if (form.value.latitude && form.value.longitude) {
                centerLat = parseFloat(form.value.latitude);
                centerLng = parseFloat(form.value.longitude);
                zoom = 13;
            }

            map.value = new window.google.maps.Map(mapElement, {
                zoom: zoom,
                center: { lat: centerLat, lng: centerLng },
                mapTypeId: "roadmap",
            });

            // Add restaurant location marker
            if (form.value.latitude && form.value.longitude) {
                const restaurantMarker = new window.google.maps.Marker({
                    position: { lat: centerLat, lng: centerLng },
                    map: map.value,
                    title: "Your Restaurant",
                    icon: {
                        url: "https://maps.google.com/mapfiles/ms/icons/red-dot.png",
                        scaledSize: new window.google.maps.Size(32, 32),
                    },
                });

                const infoWindow = new window.google.maps.InfoWindow({
                    content: `
            <div style="padding: 8px;">
              <strong>${form.value.business_name || "Your Restaurant"}</strong><br>
              <small>${form.value.address || "Restaurant Location"}</small>
            </div>
          `,
                });

                restaurantMarker.addListener("click", () => {
                    infoWindow.open(map.value, restaurantMarker);
                });
            }

            // Add click listener
            map.value.addListener("click", (event) => {
                const lat = event.latLng.lat();
                const lng = event.latLng.lng();

                if (selectedRangeType.value === "northeast") {
                    form.value.delivery_range_northeast_lat = lat.toString();
                    form.value.delivery_range_northeast_lng = lng.toString();
                } else {
                    form.value.delivery_range_southwest_lat = lat.toString();
                    form.value.delivery_range_southwest_lng = lng.toString();
                }

                addMarker(lat, lng, selectedRangeType.value);
            });

            loadExistingMarkers();
        };

        const addMarker = (lat, lng, type) => {
            mapMarkers.value = mapMarkers.value.filter(
                (marker) => marker.type !== type
            );

            const marker = new window.google.maps.Marker({
                position: { lat, lng },
                map: map.value,
                title: `${type === "northeast" ? "Northeast" : "Southwest"} Corner`,
                label: type === "northeast" ? "NE" : "SW",
            });

            mapMarkers.value.push({ marker, type });
        };

        const loadExistingMarkers = () => {
            if (
                form.value.delivery_range_northeast_lat &&
                form.value.delivery_range_northeast_lng
            ) {
                addMarker(
                    parseFloat(form.value.delivery_range_northeast_lat),
                    parseFloat(form.value.delivery_range_northeast_lng),
                    "northeast"
                );
            }

            if (
                form.value.delivery_range_southwest_lat &&
                form.value.delivery_range_southwest_lng
            ) {
                addMarker(
                    parseFloat(form.value.delivery_range_southwest_lat),
                    parseFloat(form.value.delivery_range_southwest_lng),
                    "southwest"
                );
            }
        };

        const closeMapModal = () => {
            showMapModal.value = false;
            mapMarkers.value = [];
        };

        const setSuggestedRange = (radius) => {
            if (!form.value.latitude || !form.value.longitude) {
                Swal.fire({
                    icon: "warning",
                    title: "Restaurant Location Required",
                    text: "Please set your restaurant location first in the Location Information section.",
                    timer: 3000,
                });
                return;
            }

            const restaurantLat = parseFloat(form.value.latitude);
            const restaurantLng = parseFloat(form.value.longitude);

            const radiusInKm = parseInt(radius);
            const latOffset = radiusInKm / 111.32;
            const lngOffset = radiusInKm / (111.32 * Math.cos((restaurantLat * Math.PI) / 180));

            form.value.delivery_range_northeast_lat = (restaurantLat + latOffset).toString();
            form.value.delivery_range_northeast_lng = (restaurantLng + lngOffset).toString();
            form.value.delivery_range_southwest_lat = (restaurantLat - latOffset).toString();
            form.value.delivery_range_southwest_lng = (restaurantLng - lngOffset).toString();

            if (map.value) {
                loadExistingMarkers();
                drawDeliveryAreaCircle(restaurantLat, restaurantLng, radiusInKm);
            }

            Swal.fire({
                icon: "success",
                title: "Delivery Range Set",
                text: `${radius} delivery area has been configured around your restaurant.`,
                timer: 2000,
            });
        };

        const clearDeliveryRange = () => {
            form.value.delivery_range_northeast_lat = "";
            form.value.delivery_range_northeast_lng = "";
            form.value.delivery_range_southwest_lat = "";
            form.value.delivery_range_southwest_lng = "";

            mapMarkers.value.forEach((marker) => marker.marker.setMap(null));
            mapMarkers.value = [];

            if (window.deliveryAreaCircle) {
                window.deliveryAreaCircle.setMap(null);
                window.deliveryAreaCircle = null;
            }

            Swal.fire({
                icon: "success",
                title: "Delivery Range Cleared",
                text: "Your delivery range has been cleared.",
                timer: 2000,
            });
        };

        const drawDeliveryAreaCircle = (centerLat, centerLng, radiusKm) => {
            if (window.deliveryAreaCircle) {
                window.deliveryAreaCircle.setMap(null);
            }

            window.deliveryAreaCircle = new window.google.maps.Circle({
                strokeColor: "#FF0000",
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: "#FF0000",
                fillOpacity: 0.15,
                map: map.value,
                center: { lat: centerLat, lng: centerLng },
                radius: radiusKm * 1000,
            });
        };

        const saveDeliveryRange = () => {
            closeMapModal();
            Swal.fire({
                icon: "success",
                title: "Delivery Range Updated",
                text: "Delivery range has been set successfully. Click 'Save Settings' to apply changes.",
                timer: 3000,
            });
        };

        watch(
            () => showMapModal.value,
            (isOpen) => {
                if (isOpen) {
                    nextTick(() => {
                        loadGoogleMapsScript().then(() => {
                            initMap();
                        });
                    });
                }
            }
        );

        onMounted(() => {
            fetchSettings();
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
            showMapModal,
            selectedRangeType,
            deliveryRangeNortheast,
            deliveryRangeSouthwest,
            hasExistingDeliveryRange,
            currentDeliveryRangeDisplay,
            lastUpdated,
            closeMapModal,
            saveDeliveryRange,
            setSuggestedRange,
            clearDeliveryRange,
        };
    },
};
</script>

<style scoped>
.settings-page {
    padding: 20px;
    font-family: "Inter", "Roboto", "Segoe UI", Arial, sans-serif;
}

.delivery-suggestions {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    height: fit-content;
    border: 1px solid #e9ecef;
}

.suggestion-buttons .btn {
    transition: all 0.2s ease;
}

.suggestion-buttons .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.suggestion-buttons .btn:active {
    transform: translateY(0);
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