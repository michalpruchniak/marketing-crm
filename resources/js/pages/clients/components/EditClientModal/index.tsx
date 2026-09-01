import { Form } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import ClientController from '@/actions/App/Http/Controllers/ClientController';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import type { Client } from '../../types';
import EditClientForm from './Form';

type Props = {
    client: Client;
    open: boolean;
    onOpenChange: (open: boolean) => void;
};

export default function EditClientModal({ client, open, onOpenChange }: Props) {
    const { t } = useTranslation();

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="max-h-[90vh] overflow-y-auto sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{t('clients.editClientTitle')}</DialogTitle>
                </DialogHeader>

                <Form
                    {...ClientController.update.form(client.id)}
                    options={{ preserveScroll: true }}
                    onSuccess={() => onOpenChange(false)}
                    className="space-y-4"
                >
                    {({ processing, errors }) => (
                        <EditClientForm
                            client={client}
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
