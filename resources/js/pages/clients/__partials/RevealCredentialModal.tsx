import { useEffect, useState } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Copy, Check } from 'lucide-react';

type RevealedCredential = {
    id: string;
    name: string;
    description: string | null;
    login: string;
    password: string;
    additional_information: string | null;
    url?: string | null;
};

type Props = {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    credential: RevealedCredential | null;
};

export default function RevealCredentialModal({
    open,
    onOpenChange,
    credential,
}: Props) {
    const [copiedLogin, setCopiedLogin] = useState(false);
    const [copiedPassword, setCopiedPassword] = useState(false);
    const [copiedUrl, setCopiedUrl] = useState(false);

    useEffect(() => {
        if (!open) {
            setCopiedLogin(false);
            setCopiedPassword(false);
            setCopiedUrl(false);
        }
    }, [open]);

    async function copyText(text: string | undefined, set: (v: boolean) => void) {
        if (!text) return;
        try {
            await navigator.clipboard.writeText(text);
            set(true);
            setTimeout(() => set(false), 1500);
        } catch {
            // ignore clipboard errors silently
        }
    }

    return (
        <Dialog
            open={open}
            onOpenChange={(o) => {
                onOpenChange(o);
                if (!o) return;
            }}
        >
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{credential?.name ?? 'Credential'}</DialogTitle>
                </DialogHeader>

                {credential && (
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
                                    onClick={() => copyText(credential.url ?? '', setCopiedUrl)}
                                >
                                    {copiedUrl ? <Check className="size-4" /> : <Copy className="size-4" />}
                                </Button>
                            </div>
                        )}

                        <div>
                            <div className="text-muted-foreground">Login</div>
                            <div className="flex items-center justify-between font-mono">
                                <div className="truncate">{credential.login}</div>
                                <Button size="sm" variant="ghost" onClick={() => copyText(credential.login, setCopiedLogin)}>
                                    {copiedLogin ? <Check className="size-4" /> : <Copy className="size-4" />}
                                </Button>
                            </div>
                        </div>

                        <div>
                            <div className="text-muted-foreground">Hasło</div>
                            <div className="flex items-center justify-between font-mono">
                                <div className="truncate">{credential.password}</div>
                                <Button size="sm" variant="ghost" onClick={() => copyText(credential.password, setCopiedPassword)}>
                                    {copiedPassword ? <Check className="size-4" /> : <Copy className="size-4" />}
                                </Button>
                            </div>
                        </div>

                        {credential.additional_information && (
                            <div>
                                <div className="text-muted-foreground">Dodatkowe informacje</div>
                                <div className="whitespace-pre-wrap font-mono">{credential.additional_information}</div>
                            </div>
                        )}
                    </div>
                )}
            </DialogContent>
        </Dialog>
    );
}
