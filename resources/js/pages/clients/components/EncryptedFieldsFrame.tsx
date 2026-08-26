import type { ReactNode } from 'react';
import { useTranslation } from 'react-i18next';
import { cn } from '@/lib/utils';

type Props = {
    children: ReactNode;
    className?: string;
};

export default function EncryptedFieldsFrame({ children, className }: Props) {
    const { t } = useTranslation('clients');

    return (
        <div
            className={cn(
                'space-y-4 rounded-lg border border-orange-400/70 bg-orange-50/50 p-4 dark:border-orange-500/50 dark:bg-orange-950/20',
                className,
            )}
        >
            <div className="space-y-1">
                <p className="text-sm font-medium text-orange-800 dark:text-orange-300">
                    {t('encryptedFieldsTitle')}
                </p>
                <p className="text-xs text-orange-700/80 dark:text-orange-400/80">
                    {t('encryptedFieldsDescription')}
                </p>
            </div>
            {children}
        </div>
    );
}
