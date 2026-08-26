import { Head, router } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';
import ClientController from '@/actions/App/Http/Controllers/ClientController';
import ClientCredentialController from '@/actions/App/Http/Controllers/ClientCredentialController';
import DeleteModal from '@/components/delete-modal';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { index as clientsIndex } from '@/routes/clients';
import ClientDetailsSection from './__partials/ClientDetailsSection';
import CredentialsSection from './__partials/CredentialsSection';
import AddCredentialModal from './components/AddCredentialModal';
import RevealCredentialModal from './components/RevealCredentialModal';
import type { Client, CredentialMeta, RevealedCredential } from './types';

export default function ClientsShow({
    client,
    credentials,
}: {
    client: Client;
    credentials: CredentialMeta[];
}) {
    const { t } = useTranslation();

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
                        : t('clients.revealErrorTitle'),
                );

                return;
            }

            setRevealed(data as RevealedCredential);
            setRevealOpen(true);
        } catch {
            setRevealError(t('clients.revealErrorConnection'));
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
                        description={t('clients.clientDetailsDescription')}
                    />
                    <Button
                        variant="destructive"
                        onClick={() => setDeleteClientOpen(true)}
                    >
                        <Trash2 className="size-4" />
                        {t('clients.deleteClient')}
                    </Button>
                </div>

                <ClientDetailsSection client={client} />

                <CredentialsSection
                    credentials={credentials}
                    revealError={revealError}
                    revealLoading={revealLoading}
                    onAdd={() => setCreateOpen(true)}
                    onReveal={handleReveal}
                    onDelete={setDeleteCredential}
                />
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
                title={t('clients.deleteClientConfirmTitle')}
                description={t('clients.deleteClientConfirmDescription', {
                    name: client.name,
                })}
                onConfirm={confirmDeleteClient}
            />

            <DeleteModal
                open={deleteCredential !== null}
                onOpenChange={handleDeleteCredentialOpenChange}
                title={t('clients.deleteCredentialConfirmTitle')}
                description={t('clients.deleteCredentialConfirmDescription', {
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
