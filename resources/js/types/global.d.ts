import type { Auth } from '@/types/auth';

declare module 'react' {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    interface InputHTMLAttributes<T> {
        passwordrules?: string;
    }
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            sidebarOpen: boolean;
            can: {
                clients: {
                    create: boolean;
                    assignCoordinator: boolean;
                    update?: boolean;
                    delete?: boolean;
                };
                credentials: {
                    view: boolean;
                    create: boolean;
                    reveal: boolean;
                    delete: boolean;
                };
            };
            coordinators?: Array<{
                id: number;
                name: string;
            }>;
            [key: string]: unknown;
        };
    }
}
