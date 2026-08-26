import { Form } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import ClientCredentialController from '@/actions/App/Http/Controllers/ClientCredentialController';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import AddCredentialForm from './Form';

type Props = {
    clientId: string;
    open: boolean;
    onOpenChange: (open: boolean) => void;
};

export default function AddCredentialModal({
    clientId,
    open,
    onOpenChange,
}: Props) {
    const { t } = useTranslation();

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="max-h-[90vh] overflow-y-auto sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{t('clients.addCredentialTitle')}</DialogTitle>
                </DialogHeader>

                <Form
                    {...ClientCredentialController.store.form(clientId)}
                    options={{ preserveScroll: true }}
                    resetOnSuccess
                    onSuccess={() => onOpenChange(false)}
                    className="space-y-4"
                >
                    {({ processing, errors }) => (
                        <AddCredentialForm
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
