import { Plus } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import Heading from '@/components/heading';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import CredentialsTable from '../components/CredentialsTable';
import type { CredentialMeta } from '../types';

type Props = {
    credentials: CredentialMeta[];
    revealError: string | null;
    revealLoading: boolean;
    onAdd: () => void;
    onReveal: (credentialId: string) => void;
    onDelete: (credential: CredentialMeta) => void;
};

export default function CredentialsSection({
    credentials,
    revealError,
    revealLoading,
    onAdd,
    onReveal,
    onDelete,
}: Props) {
    const { t } = useTranslation();

    return (
        <section className="space-y-4">
            <div className="flex flex-wrap items-start justify-between gap-4">
                <Heading
                    variant="small"
                    title={t('clients.credentialsTitle')}
                    description={t('clients.credentialsDescription')}
                />
                <Button type="button" onClick={onAdd}>
                    <Plus className="size-4" />
                    {t('clients.addCredential')}
                </Button>
            </div>

            {revealError && (
                <Alert variant="destructive">
                    <AlertTitle>{t('clients.revealErrorTitle')}</AlertTitle>
                    <AlertDescription>{revealError}</AlertDescription>
                </Alert>
            )}

            {credentials.length === 0 ? (
                <p className="text-sm text-muted-foreground">
                    {t('clients.noCredentials')}
                </p>
            ) : (
                <CredentialsTable
                    credentials={credentials}
                    revealLoading={revealLoading}
                    onReveal={onReveal}
                    onDelete={onDelete}
                />
            )}
        </section>
    );
}
