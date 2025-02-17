<script>
import { defineComponent } from "vue";
import { RouterView, useRouter } from "vue-router";
import { useSetting } from "@/services";
import { useAuthStore } from "@/stores/auth";
import Swal from "sweetalert2";

export default {
    setup() {
        const { data: setting = {} } = useSetting();
        const auth = localStorage.getItem("auth_key");
        const store = useAuthStore();
        return {
            setting,
            auth,
            store,
        };
    },
    data() {
        return {
            isOpen: false,
            scrolled: false,
        };
    },
    methods: {
        toggle() {
            this.isOpen = !this.isOpen;
        },
        handleResize() {
            if (window.innerWidth >= 768) {
                this.isOpen = false;
            }
        },
        handleScroll() {
            this.scrolled = window.scrollY > 50;
        },
        signOut() {
            Swal.fire({
                icon: "warning",
                text: "Are you sure you want to leave?",
                showCancelButton: true,
                confirmButtonText: "Yes, Get Out",
                cancelButtonText: "No",
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: "btn fw-semibold btn-light-primary",
                    cancelButton: "btn fw-semibold btn-light-danger",
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    this.store.logout();
                    Swal.fire({
                        icon: "success",
                        text: "Exit Succesful",
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        },
    },
    mounted() {
        window.addEventListener("resize", this.handleResize);
        window.addEventListener("scroll", this.handleScroll);
    },
    beforeDestroy() {
        window.removeEventListener("resize", this.handleResize);
        window.removeEventListener("scroll", this.handleScroll);
    },
};
</script>

<template>
    <main>
        <nav
            :class="[
                'navbar navbar-expand-lg fixed-top transition-all',
                scrolled ? 'scrolled' : '',
            ]"
        >
            <div class="container">
                <a class="navbar-brand" href="/landing/page">
                    <img src="/media/misc/logoss.png" alt="Logo" height="50" />
                </a>
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div
                    class="collapse navbar-collapse"
                    id="navbarSupportedContent"
                >
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a
                                class="nav-link"
                                aria-current="page"
                                href="/landing/page"
                                >Home</a
                            >
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/landing/page/history"
                                >History</a
                            >
                        </li>
                        <li class="nav-item dropdown">
                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                Services
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#"
                                        >Service 1</a
                                    >
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#"
                                        >Service 2</a
                                    >
                                </li>
                                <li><hr class="dropdown-divider" /></li>
                                <li>
                                    <a class="dropdown-item" href="#"
                                        >Custom Service</a
                                    >
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Contact</a>
                        </li>
                        <li v-if="store.isAuthenticated" class="nav-item">
                            <a @click.prevent="signOut" class="nav-link">
                                Logout
                            </a>
                        </li>
                        <li v-else class="nav-item ms-2">
                            <router-link class="btn btn-primary" to="/sign-in"
                                >Sign In</router-link
                            >
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <RouterView style="min-height: 100vh; padding-top: 80px" />
        <footer class="footer">
            <div class="footer-content">
                <div class="container">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <h5 class="footer-title">About Us</h5>
                            <p class="footer-text">
                                Your company description goes here. Make it
                                engaging and informative.
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h5 class="footer-title">Quick Links</h5>
                            <ul class="footer-links">
                                <li><a href="#">Home</a></li>
                                <li><a href="#">Services</a></li>
                                <li><a href="#">About Us</a></li>
                                <li><a href="#">Contact</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h5 class="footer-title">Contact Info</h5>
                            <ul class="footer-contact">
                                <li>📍 Your Address Here</li>
                                <li>📧 email@example.com</li>
                                <li>📞 +1 234 567 890</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <p class="mb-0">
                                © 2024 Your Company. All rights reserved.
                            </p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <a href="#" class="me-3">Privacy Policy</a>
                            <a href="#">Terms of Service</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </main>
</template>

<style scoped>
/* Navbar Styles */
.navbar {
    transition: all 0.3s ease;
    padding: 1rem 0;
}

.navbar:not(.scrolled) {
    background: transparent;
}

.navbar.scrolled {
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 0.5rem 0;
}

.navbar:not(.scrolled) .nav-link {
    color: #fff;
}

.navbar.scrolled .nav-link {
    color: #333;
}

.navbar:not(.scrolled) .navbar-toggler-icon {
    filter: invert(1);
}

.nav-link {
    font-weight: 500;
    padding: 0.5rem 1rem !important;
    transition: all 0.3s ease;
    position: relative;
}

.nav-link::after {
    content: "";
    position: absolute;
    left: 50%;
    bottom: 0;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background: linear-gradient(to right, #007bff, #00bcd4);
    transition: width 0.3s ease;
}

.nav-link:hover::after {
    width: 100%;
}

/* Footer Styles */
.footer {
    background-color: #1a1a1a;
    color: #fff;
    margin-top: 5rem;
}

.footer-content {
    padding: 4rem 0;
}

.footer-title {
    color: #fff;
    font-weight: 600;
    margin-bottom: 1.5rem;
    position: relative;
}

.footer-title::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -8px;
    width: 40px;
    height: 2px;
    background: linear-gradient(to right, #007bff, #00bcd4);
}

.footer-text {
    color: #999;
    line-height: 1.6;
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 0.75rem;
}

.footer-links a {
    color: #999;
    text-decoration: none;
    transition: all 0.3s ease;
}

.footer-links a:hover {
    color: #fff;
    padding-left: 5px;
}

.footer-contact {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-contact li {
    color: #999;
    margin-bottom: 0.75rem;
}

.footer-bottom {
    background-color: #111;
    padding: 1.5rem 0;
    font-size: 0.9rem;
}

.footer-bottom a {
    color: #999;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-bottom a:hover {
    color: #fff;
}

@media (max-width: 768px) {
    .footer-bottom {
        text-align: center;
    }

    .footer-bottom .text-md-end {
        text-align: center !important;
        margin-top: 1rem;
    }
}
</style>
