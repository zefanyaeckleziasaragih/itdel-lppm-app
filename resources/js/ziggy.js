const Ziggy = {
    url: "http://localhost:8000",
    port: 8000,
    defaults: {},
    routes: {
        "api.fetch-users": {
            uri: "api/fetch-users",
            methods: ["POST"],
        },

        // SSO
        "sso.callback": {
            uri: "sso/callback",
            methods: ["GET", "HEAD"],
        },

        // Auth
        "auth.login": {
            uri: "auth/login",
            methods: ["GET", "HEAD"],
        },
        "auth.login-check": {
            uri: "auth/login-check",
            methods: ["POST"],
        },
        "auth.login-post": {
            uri: "auth/login-post",
            methods: ["POST"],
        },
        "auth.logout": {
            uri: "auth/logout",
            methods: ["GET", "HEAD"],
        },
        "auth.totp": {
            uri: "auth/totp",
            methods: ["GET", "HEAD"],
        },
        "auth.totp-post": {
            uri: "auth/totp-post",
            methods: ["POST"],
        },

        // Home
        home: {
            uri: "/",
            methods: ["GET", "HEAD"],
        },

        // Hak Akses
        "hak-akses": {
            uri: "hak-akses",
            methods: ["GET", "HEAD"],
        },
        "hak-akses.change-post": {
            uri: "hak-akses/change",
            methods: ["POST"],
        },
        "hak-akses.delete-post": {
            uri: "hak-akses/delete",
            methods: ["POST"],
        },
        "hak-akses.delete-selected-post": {
            uri: "hak-akses/delete-selected",
            methods: ["POST"],
        },

        // Todo
        todo: {
            uri: "todo",
            methods: ["GET", "HEAD"],
        },
        "todo.change-post": {
            uri: "todo/change",
            methods: ["POST"],
        },
        "todo.delete-post": {
            uri: "todo/delete",
            methods: ["POST"],
        },

        // =========================
        // PENGHARGAAN – STATISTIK & DAFTAR (dosen)
        // =========================
        "penghargaan.statistik": {
            uri: "penghargaan/statistik",
            methods: ["GET", "HEAD"],
        },
        "penghargaan.daftar": {
            uri: "penghargaan/daftar",
            methods: ["GET", "HEAD"],
        },
        "penghargaan.detail": {
            uri: "penghargaan/daftar/{id}",
            methods: ["GET", "HEAD"],
            parameters: ["id"],
        },
        "penghargaan.konfirmasi": {
            uri: "penghargaan/daftar/{id}/konfirmasi",
            methods: ["POST"],
            parameters: ["id"],
        },

        // =========================
        // PENGHARGAAN – JURNAL
        // (pengajuan jurnal penghargaan)
        // =========================
        "journal.create": {
            uri: "penghargaan/jurnal/pengajuan",
            methods: ["GET", "HEAD"],
        },
        "journal.store": {
            uri: "penghargaan/jurnal/pengajuan",
            methods: ["POST"],
        },

        // =========================
        // PENGHARGAAN – SEMINAR
        // =========================
        "penghargaan.seminar.daftar": {
            uri: "penghargaan/seminar/daftar",
            methods: ["GET", "HEAD"],
        },
        "penghargaan.seminar.pilih": {
            uri: "penghargaan/seminar/pilih",
            methods: ["GET", "HEAD"],
        },
        "penghargaan.seminar": {
            uri: "penghargaan/seminar",
            methods: ["GET", "HEAD", "POST"],
        },
        "penghargaan.seminar.store": {
            uri: "penghargaan/seminar",
            methods: ["POST"],
        },

        // =========================
        // DAFTAR PENGHARGAAN (menu Admin)
        // =========================
        "daftar-penghargaan": {
            uri: "daftar-penghargaan",
            methods: ["GET", "HEAD"],
        },
        "daftar-penghargaan.detail": {
            uri: "daftar-penghargaan/{id}",
            methods: ["GET", "HEAD"],
            parameters: ["id"],
        },

        // Storage
        "storage.local": {
            uri: "storage/{path}",
            methods: ["GET", "HEAD"],
            wheres: { path: ".*" },
            parameters: ["path"],
        },
    },
};

if (typeof window !== "undefined" && typeof window.Ziggy !== "undefined") {
    Object.assign(Ziggy.routes, window.Ziggy.routes);
}

export { Ziggy };
