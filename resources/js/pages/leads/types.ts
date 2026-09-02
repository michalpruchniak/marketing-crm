export type LeadLabelOption = {
    value: string;
};

export type SalesPersonOption = {
    id: number;
    name: string;
};

export type Lead = {
    id: number;
    name: string;
    email: string | null;
    phone: string | null;
    notes: string | null;
    label: string;
    sales_id: number | null;
    created_at?: string;
    sales_person?: {
        id: number;
        name: string;
    } | null;
};

export type LeadListItem = Lead;
