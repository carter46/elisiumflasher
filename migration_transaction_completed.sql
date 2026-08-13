-- Forward-only: add COMPLETED to local_transactions.status ENUM.
-- Debit statuses: SUCCESSFUL, PENDING, COMPLETED
-- Non-debit (restore on transition): FAILED, REVERSED

ALTER TABLE local_transactions
  MODIFY COLUMN status ENUM('SUCCESSFUL','FAILED','PENDING','REVERSED','COMPLETED') NOT NULL DEFAULT 'SUCCESSFUL';
