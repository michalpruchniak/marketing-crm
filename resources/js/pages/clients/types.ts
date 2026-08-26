export type Client = {
    id: string;
    name: string;
    email: string | null;
    phone: string | null;
    notes: string | null;
};

export type CredentialMeta = {
    id: string;
    name: string;
    description: string | null;
    type: string;
};

export type RevealedCredential = {
    id: string;
    name: string;
    description: string | null;
    login: string;
    password: string;
    additional_information: string | null;
    url?: string | null;
};
