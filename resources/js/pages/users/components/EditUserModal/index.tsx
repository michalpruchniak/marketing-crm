import { Form } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import UserController from '@/actions/App/Http/Controllers/UserController';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import type { RoleOption, UserListItem } from '../../types';
import EditUserForm from './Form';

type Props = {
    user: UserListItem;
    roles: RoleOption[];
    open: boolean;
    onOpenChange: (open: boolean) => void;
};

export default function EditUserModal({
    user,
    roles,
    open,
    onOpenChange,
}: Props) {
    const { t } = useTranslation();

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="max-h-[90vh] overflow-y-auto sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{t('users.editTitle')}</DialogTitle>
                </DialogHeader>

                <Form
                    {...UserController.update.form(user.id)}
                    options={{ preserveScroll: true }}
                    onSuccess={() => onOpenChange(false)}
                    className="space-y-4"
                >
                    {({ processing, errors }) => (
                        <EditUserForm
                            user={user}
                            roles={roles}
                            processing={processing}
                            errors={errors}
                            onCancel={() => onOpenChange(false)}
                        />
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
