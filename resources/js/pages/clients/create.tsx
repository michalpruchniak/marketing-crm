import { Form, Head, Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import ClientController from '@/actions/App/Http/Controllers/ClientController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import {
    index as clientsIndex,
    create as clientsCreate,
} from '@/routes/clients';
import FieldLabel from './components/FieldLabel';

export default function ClientsCreate() {
    const { t } = useTranslation();

    return (
        <>
            <Head title={t('clients.createTitle')} />

            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <Heading
                    title={t('clients.createTitle')}
                    description={t('clients.createDescription')}
                />

                <Form
                    {...ClientController.store.form()}
                    className="max-w-xl space-y-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <FieldLabel htmlFor="name" required>
                                    {t('common.name')}
                                </FieldLabel>
                                <Input
                                    id="name"
                                    name="name"
                                    required
                                    autoFocus
                                    placeholder={t('clients.namePlaceholder')}
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <FieldLabel htmlFor="email">
                                    {t('common.email')}
                                </FieldLabel>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    placeholder={t('clients.emailPlaceholder')}
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <FieldLabel htmlFor="phone">
                                    {t('common.phone')}
                                </FieldLabel>
                                <Input
                                    id="phone"
                                    name="phone"
                                    placeholder={t('clients.phonePlaceholder')}
                                />
                                <InputError message={errors.phone} />
                            </div>

                            <div className="grid gap-2">
                                <FieldLabel htmlFor="notes">
                                    {t('common.notes')}
                                </FieldLabel>
                                <Textarea
                                    id="notes"
                                    name="notes"
                                    placeholder={t('clients.notesPlaceholder')}
                                />
                                <InputError message={errors.notes} />
                            </div>

                            <div className="flex gap-3">
                                <Button type="submit" disabled={processing}>
                                    {t('clients.saveClient')}
                                </Button>
                                <Button variant="outline" asChild>
                                    <Link href={clientsIndex()}>
                                        {t('common.cancel')}
                                    </Link>
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

ClientsCreate.layout = {
    breadcrumbs: [
        {
            title: 'Clients',
            href: clientsIndex(),
        },
        {
            title: 'Add client',
            href: clientsCreate(),
        },
    ],
};
