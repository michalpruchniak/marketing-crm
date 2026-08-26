import { Check, Copy } from 'lucide-react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { Button } from '@/components/ui/button';
import type { RevealedCredential } from '../../types';

type Props = {
    credential: RevealedCredential;
};

export default function RevealCredentialForm({ credential }: Props) {
    const { t } = useTranslation();

    const [copiedLogin, setCopiedLogin] = useState(false);
    const [copiedPassword, setCopiedPassword] = useState(false);
    const [copiedUrl, setCopiedUrl] = useState(false);

    async function copyText(
        text: string | undefined,
        set: (v: boolean) => void,
    ) {
        if (!text) {
            return;
        }

        try {
            await navigator.clipboard.writeText(text);
            set(true);
            setTimeout(() => set(false), 1500);
        } catch {
            // ignore clipboard errors silently
        }
    }

    return (
        <div className="space-y-3 text-sm">
            {credential.url && (
                <div className="flex items-center justify-between gap-2">
                    <div className="truncate text-muted-foreground">
                        <a
                            href={credential.url}
                            target="_blank"
                            rel="noreferrer"
                            className="underline"
                        >
                            {credential.url}
                        </a>
                    </div>
                    <Button
                        size="sm"
                        variant="ghost"
                        onClick={() =>
                            copyText(credential.url ?? '', setCopiedUrl)
                        }
                    >
                        {copiedUrl ? (
                            <Check className="size-4" />
                        ) : (
                            <Copy className="size-4" />
                        )}
                    </Button>
                </div>
            )}

            <div>
                <div className="text-muted-foreground">{t('common.login')}</div>
                <div className="flex items-center justify-between font-mono">
                    <div className="truncate">{credential.login}</div>
                    <Button
                        size="sm"
                        variant="ghost"
                        onClick={() =>
                            copyText(credential.login, setCopiedLogin)
                        }
                    >
                        {copiedLogin ? (
                            <Check className="size-4" />
                        ) : (
                            <Copy className="size-4" />
                        )}
                    </Button>
                </div>
            </div>

            <div>
                <div className="text-muted-foreground">{t('common.password')}</div>
                <div className="flex items-center justify-between font-mono">
                    <div className="truncate">{credential.password}</div>
                    <Button
                        size="sm"
                        variant="ghost"
                        onClick={() =>
                            copyText(credential.password, setCopiedPassword)
                        }
                    >
                        {copiedPassword ? (
                            <Check className="size-4" />
                        ) : (
                            <Copy className="size-4" />
                        )}
                    </Button>
                </div>
            </div>

            {credential.additional_information && (
                <div>
                    <div className="text-muted-foreground">
                        {t('common.additionalInformation')}
                    </div>
                    <div className="font-mono whitespace-pre-wrap">
                        {credential.additional_information}
                    </div>
                </div>
            )}
        </div>
    );
}
