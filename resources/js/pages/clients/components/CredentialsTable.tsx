import { Eye, KeyRound, Trash2 } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { Button } from '@/components/ui/button';
import type { CredentialMeta } from '../types';

type Props = {
    credentials: CredentialMeta[];
    revealLoading: boolean;
    onReveal: (credentialId: string) => void;
    onDelete: (credential: CredentialMeta) => void;
};

export default function CredentialsTable({
    credentials,
    revealLoading,
    onReveal,
    onDelete,
}: Props) {
    const { t } = useTranslation();

    return (
        <div className="max-h-[min(24rem,60vh)] overflow-auto rounded-xl border">
            <table className="w-full min-w-[40rem] text-left text-sm">
                <thead className="sticky top-0 border-b bg-muted/40">
                    <tr>
                        <th className="px-4 py-3 font-medium">
                            {t('common.name')}
                        </th>
                        <th className="px-4 py-3 font-medium">
                            {t('common.description')}
                        </th>
                        <th className="px-4 py-3 font-medium">
                            {t('clients.credTableHeadType')}
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
                                    <KeyRound className="size-4 shrink-0 text-muted-foreground" />
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
                                        onClick={() => onReveal(credential.id)}
                                    >
                                        <Eye className="size-4" />
                                        {t('common.show')}
                                    </Button>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => onDelete(credential)}
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
    );
}
