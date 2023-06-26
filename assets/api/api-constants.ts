export default {
    BASE: '',
    FORM: {
        LOGIN: '/login',
    },
    AUTH: {
        LOGIN: '/api/security/login',
        LOGOUT: '/api/security/logout',
    },
    LINK: {
        DEFAULT: '/api/v1/link/list',
        CREATE:  '/api/v1/link/create',
        OPERATION: (id: number): string => `/api/v1/link/${id}`,
        MODERATION: (id: number, status: string): string => `/api/v1/link/${id}?status=${status}`,
    },
    USER: {
        DEFAULT: '/api/v1/user/list',
        CURRENT_USER: '/api/v1/user',
        OPERATION: (id: number): string => `/api/v1/user/${id}`,
        CHANGE_ROLE: (id: number, action: string): string => `/api/v1/user/${id}?action=${action}`,
    }
};