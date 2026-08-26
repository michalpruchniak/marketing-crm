import { useTranslation } from 'react-i18next';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import type { RevealedCredential } from '../../types';
import RevealCredentialForm from './Form';

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
    const { t } = useTranslation();

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>
                        {credential?.name ?? t('clients.revealModalTitle')}
                    </DialogTitle>
                </DialogHeader>

                {credential && <RevealCredentialForm credential={credential} />}
            </DialogContent>
        </Dialog>
    );
}
