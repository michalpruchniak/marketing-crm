import { Head, router } from '@inertiajs/react';
import { Eye, KeyRound, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';
import ClientController from '@/actions/App/Http/Controllers/ClientController';
import ClientCredentialController from '@/actions/App/Http/Controllers/ClientCredentialController';
import DeleteModal from '@/components/delete-modal';
import Heading from '@/components/heading';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { index as clientsIndex } from '@/routes/clients';
import AddCredentialModal from './__partials/AddCredentialModal';
import RevealCredentialModal from './__partials/RevealCredentialModal';

type Client = {
    id: string;
    name: string;
    email: string | null;
    phone: string | null;
    notes: string | null;
};

type CredentialMeta = {
    id: string;
    name: string;
    description: string | null;
    type: string;
};

type RevealedCredential = {
    id: string;
    name: string;
    description: string | null;
    login: string;
    password: string;
    additional_information: string | null;
    url?: string | null;
};

export default function ClientsShow({
    client,
    credentials,
}: {
    client: Client;
    credentials: CredentialMeta[];
}) {
    const { t } = useTranslation('clients');
    const { t: tc } = useTranslation('common');

    const [createOpen, setCreateOpen] = useState(false);
    const [revealed, setRevealed] = useState<RevealedCredential | null>(null);
    const [revealOpen, setRevealOpen] = useState(false);
    const [revealLoading, setRevealLoading] = useState(false);
    const [revealError, setRevealError] = useState<string | null>(null);
    const [deleteClientOpen, setDeleteClientOpen] = useState(false);
    const [deleteCredential, setDeleteCredential] =
        useState<CredentialMeta | null>(null);

    async function handleReveal(credentialId: string) {
        setRevealLoading(true);
        setRevealError(null);

        try {
            const url = ClientCredentialController.reveal.url({
                client: client.id,
                credential: credentialId,
            });

            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            const data = (await response.json()) as
                RevealedCredential | { message?: string };

            if (!response.ok) {
                setRevealError(
                    'message' in data && data.message
                        ? data.message
                        : t('revealErrorTitle'),
                );

                return;
            }

            setRevealed(data as RevealedCredential);
            setRevealOpen(true);
        } catch {
            setRevealError(t('revealErrorConnection'));
        } finally {
            setRevealLoading(false);
        }
    }

    function confirmDeleteCredential() {
        if (!deleteCredential) {
            return;
        }

        router.delete(
            ClientCredentialController.destroy.url({
                client: client.id,
                credential: deleteCredential.id,
            }),
            { preserveScroll: true },
        );
    }

    function confirmDeleteClient() {
        router.delete(ClientController.destroy.url(client.id));
    }

    function handleRevealOpenChange(open: boolean) {
        setRevealOpen(open);

        if (!open) {
            setRevealed(null);
        }
    }

    function handleDeleteCredentialOpenChange(open: boolean) {
        if (!open) {
            setDeleteCredential(null);
        }
    }

    return (
        <>
            <Head title={client.name} />

            <div className="flex h-full flex-1 flex-col gap-8 p-4">
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <Heading
                        title={client.name}
                        description={t('clientDetailsDescription')}
                    />
                    <Button
                        variant="destructive"
                        onClick={() => setDeleteClientOpen(true)}
                    >
                        <Trash2 className="size-4" />
                        {t('deleteClient')}
                    </Button>
                </div>

                <section className="grid max-w-3xl gap-3 rounded-xl border p-4 text-sm">
                    <div>
                        <span className="text-muted-foreground">
                            {tc('email')}:{' '}
                        </span>
                        {client.email ?? '—'}
                    </div>
                    <div>
                        <span className="text-muted-foreground">
                            {tc('phone')}:{' '}
                        </span>
                        {client.phone ?? '—'}
                    </div>
                    <div>
                        <span className="text-muted-foreground">
                            {tc('notes')}:{' '}
                        </span>
                        {client.notes ?? '—'}
                    </div>
                </section>

                <section className="space-y-4">
                    <div className="flex flex-wrap items-start justify-between gap-4">
                        <Heading
                            variant="small"
                            title={t('credentialsTitle')}
                            description={t('credentialsDescription')}
                        />
                        <Button
                            type="button"
                            onClick={() => setCreateOpen(true)}
                        >
                            <Plus className="size-4" />
                            {t('addCredential')}
                        </Button>
                    </div>

                    {revealError && (
                        <Alert variant="destructive">
                            <AlertTitle>{t('revealErrorTitle')}</AlertTitle>
                            <AlertDescription>{revealError}</AlertDescription>
                        </Alert>
                    )}

                    {credentials.length === 0 ? (
                        <p className="text-sm text-muted-foreground">
                            {t('noCredentials')}
                        </p>
                    ) : (
                        <div className="overflow-hidden rounded-xl border">
                            <table className="w-full text-left text-sm">
                                <thead className="border-b bg-muted/40">
                                    <tr>
                                        <th className="px-4 py-3 font-medium">
                                            {tc('name')}
                                        </th>
                                        <th className="px-4 py-3 font-medium">
                                            {tc('description')}
                                        </th>
                                        <th className="px-4 py-3 font-medium">
                                            {t('credTableHeadType')}
                                        </th>
                                        <th className="px-4 py-3 font-medium" />
                                    </tr>
                                </thead>
                                <tbody>
                                    {credentials.map((credential) => (
                                        <tr
                                            key={credential.id}
                                            className="border-b last:border-0"
                                        >
                                            <td className="px-4 py-3 font-medium">
                                                <span className="inline-flex items-center gap-2">
                                                    <KeyRound className="size-4 text-muted-foreground" />
                                                    {credential.name}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3 text-muted-foreground">
                                                {credential.description ?? '—'}
                                            </td>
                                            <td className="px-4 py-3 text-muted-foreground">
                                                {credential.type}
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="flex justify-end gap-2">
                                                    <Button
                                                        type="button"
                                                        variant="outline"
                                                        size="sm"
                                                        disabled={revealLoading}
                                                        onClick={() =>
                                                            handleReveal(
                                                                credential.id,
                                                            )
                                                        }
                                                    >
                                                        <Eye className="size-4" />
                                                        {tc('show')}
                                                    </Button>
                                                    <Button
                                                        type="button"
                                                        variant="ghost"
                                                        size="sm"
                                                        onClick={() =>
                                                            setDeleteCredential(
                                                                credential,
                                                            )
                                                        }
                                                    >
                                                        <Trash2 className="size-4" />
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </section>
            </div>

            <AddCredentialModal
                clientId={client.id}
                open={createOpen}
                onOpenChange={setCreateOpen}
            />

            <RevealCredentialModal
                open={revealOpen}
                onOpenChange={handleRevealOpenChange}
                credential={revealed}
            />

            <DeleteModal
                open={deleteClientOpen}
                onOpenChange={setDeleteClientOpen}
                title={t('deleteClientConfirmTitle')}
                description={t('deleteClientConfirmDescription', {
                    name: client.name,
                })}
                onConfirm={confirmDeleteClient}
            />

            <DeleteModal
                open={deleteCredential !== null}
                onOpenChange={handleDeleteCredentialOpenChange}
                title={t('deleteCredentialConfirmTitle')}
                description={t('deleteCredentialConfirmDescription', {
                    name: deleteCredential?.name ?? '',
                })}
                onConfirm={confirmDeleteCredential}
            />
        </>
    );
}

ClientsShow.layout = {
    breadcrumbs: [
        {
            title: 'Clients',
            href: clientsIndex(),
        },
        {
            title: 'Client',
            href: '#',
        },
    ],
};
