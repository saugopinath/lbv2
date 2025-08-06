import "./bootstrap";
import "../../vendor/rappasoft/laravel-livewire-tables/resources/imports/laravel-livewire-tables-all.js";
document.addEventListener("alpine:init", () => {
    // Register your Alpine plugins or custom directives
    Alpine.store("app", {
        sidebar: false,
        fullscreen: false,
        mode: "light",
        toggleSidebar() {
            this.sidebar = !this.sidebar;
        },
        toggleFullscreen() {
            this.fullscreen = !this.fullscreen;
        },
        toggleMode() {
            this.mode = this.mode === "light" ? "dark" : "light";
        },
    });
});
window.main = function () {
    return {
        sidebarOpen: false,
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        },
    };
};

window.dropdown = function () {
    return {
        open: false,
        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
        },
    };
};
