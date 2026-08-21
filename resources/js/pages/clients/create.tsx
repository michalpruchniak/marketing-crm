import { Form, Head, Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import ClientController from '@/actions/App/Http/Controllers/ClientController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { index as clientsIndex, create as clientsCreate } from '@/routes/clients';

export default function ClientsCreate() {
    const { t } = useTranslation('clients');
    const { t: tc } = useTranslation('common');

    return (
        <>
            <Head title={t('createTitle')} />

            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <Heading title={t('createTitle')} description={t('createDescription')} />

                <Form {...ClientController.store.form()} className="max-w-xl space-y-6">
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="name">{tc('name')}</Label>
                                <Input
                                    id="name"
                                    name="name"
                                    required
                                    autoFocus
                                    placeholder={t('namePlaceholder')}
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">{tc('email')}</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    placeholder={t('emailPlaceholder')}
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="phone">{tc('phone')}</Label>
                                <Input
                                    id="phone"
                                    name="phone"
                                    placeholder={t('phonePlaceholder')}
                                />
                                <InputError message={errors.phone} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="notes">{tc('notes')}</Label>
                                <Textarea
                                    id="notes"
                                    name="notes"
                                    placeholder={t('notesPlaceholder')}
                                />
                                <InputError message={errors.notes} />
                            </div>

                            <div className="flex gap-3">
                                <Button type="submit" disabled={processing}>
                                    {t('saveClient')}
                                </Button>
                                <Button variant="outline" asChild>
                                    <Link href={clientsIndex()}>{tc('cancel')}</Link>
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
