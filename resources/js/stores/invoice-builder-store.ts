import type { InvoiceItem } from '@/types';
import { create } from 'zustand';
import { persist } from 'zustand/middleware';

export interface InvoiceDraft {
    client_id?: number;
    project_id?: number;
    currency_code: string;
    issue_date?: string;
    due_date?: string;
    notes?: string;
    terms?: string;
    client_notes?: string;
    items: InvoiceItem[];
    tax_rate?: number;
}

interface InvoiceBuilderState {
    drafts: Record<string, InvoiceDraft>;
    saveDraft: (draftId: string, draft: InvoiceDraft) => void;
    getDraft: (draftId: string) => InvoiceDraft | undefined;
    clearDraft: (draftId: string) => void;
    clearAllDrafts: () => void;
}

export const useInvoiceBuilderStore = create<InvoiceBuilderState>()(
    persist(
        (set, get) => ({
            drafts: {},

            saveDraft: (draftId: string, draft: InvoiceDraft) => {
                set((state) => ({
                    drafts: {
                        ...state.drafts,
                        [draftId]: draft,
                    },
                }));
            },

            getDraft: (draftId: string) => {
                return get().drafts[draftId];
            },

            clearDraft: (draftId: string) => {
                set((state) => {
                    const { [draftId]: _, ...remainingDrafts } = state.drafts;
                    return { drafts: remainingDrafts };
                });
            },

            clearAllDrafts: () => {
                set({ drafts: {} });
            },
        }),
        {
            name: 'invoice-builder-storage',
        },
    ),
);
