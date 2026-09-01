export type UserListItem = {
    id: number;
    name: string;
    email: string;
    banned_at: string | null;
    roles: string[];
    created_at: string;
};

export type RoleOption = {
    value: string;
    label: string;
};
