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
                canClientsCreate: boolean;
                canClientsAssignCoordinator: boolean;
                canCredentialsView: boolean;
                canCredentialsCreate: boolean;
                canCredentialsReveal: boolean;
                canCredentialsDelete: boolean;
                canClientsUpdate?: boolean;
                canClientsDelete?: boolean;
                canUsersView: boolean;
                canUsersCreate: boolean;
                canUsersUpdate: boolean;
                canUsersDelete: boolean;
                canUsersBan: boolean;
            };
            coordinators?: Array<{
                id: number;
                name: string;
            }>;
            roles?: Array<{
                value: string;
                label: string;
            }>;
            [key: string]: unknown;
        };
    }
}
