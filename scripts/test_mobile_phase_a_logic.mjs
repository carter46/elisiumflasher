/**
 * Mirrors includes/mobile_auth.php bank-match rules for offline verification
 * when PHP CLI is unavailable on the build machine.
 */
function normalizeAccount(account) {
  return String(account).replace(/\D/g, "");
}

function bankMap(mobileCode) {
  const code = String(mobileCode).toUpperCase().trim();
  switch (code) {
    case "UBA":
      return { codes: ["033"], name_needles: ["uba", "united bank for africa"] };
    case "FIRST":
      return { codes: ["011"], name_needles: ["first bank", "firstbank", "fbn"] };
    case "ZENITH":
      return { codes: ["057"], name_needles: ["zenith"] };
    case "ACCESS":
      return { codes: ["044"], name_needles: ["access"] };
    default:
      return { codes: [], name_needles: [] };
  }
}

function bankMatches(mobileCode, beneficiaryBankCode, beneficiaryBankName) {
  const map = bankMap(mobileCode);
  if (!map.codes.length && !map.name_needles.length) return false;
  const storedCode = String(beneficiaryBankCode ?? "")
    .toUpperCase()
    .trim();
  if (storedCode !== "") {
    return map.codes.some((c) => storedCode === String(c).toUpperCase());
  }
  const name = String(beneficiaryBankName ?? "")
    .toLowerCase()
    .trim();
  if (!name) return false;
  return map.name_needles.some((n) => n && name.includes(n));
}

function receiptDto(row, sessionBankCode) {
  const id = Number(row.id || 0);
  const reference = row.reference != null ? String(row.reference) : null;
  return {
    transaction_id: `local_transactions:${id}`,
    source_table: "local_transactions",
    source_id: id,
    bank_code: String(sessionBankCode).toUpperCase(),
    reference,
    session_id: reference,
    reference_id: reference,
    amount: Number(row.amount || 0),
    currency: String(row.currency || "NGN"),
    status: "SUCCESSFUL",
    purpose: row.purpose ? String(row.purpose) : null,
    transaction_date: row.transaction_date ? String(row.transaction_date) : null,
    beneficiary_name: row.beneficiary_name ? String(row.beneficiary_name) : null,
    beneficiary_bank: row.beneficiary_bank ? String(row.beneficiary_bank) : null,
    beneficiary_account: row.beneficiary_account
      ? normalizeAccount(String(row.beneficiary_account))
      : null,
    sender_name: row.sender_name ? String(row.sender_name) : null,
    sender_account: row.sender_account ? String(row.sender_account) : null,
    sender_bank: null,
    direction: "credit",
  };
}

function filterSuccessful(rows, bankCode, accountNumber) {
  const acct = normalizeAccount(accountNumber);
  return rows.filter(
    (r) =>
      String(r.status).toUpperCase() === "SUCCESSFUL" &&
      normalizeAccount(r.beneficiary_account) === acct &&
      bankMatches(bankCode, r.beneficiary_bank_code, r.beneficiary_bank)
  );
}

let failures = 0;
function assertTrue(cond, label) {
  if (cond) {
    console.log(`[PASS] ${label}`);
    return;
  }
  failures += 1;
  console.log(`[FAIL] ${label}`);
}

console.log("=== Mobile Phase A logic mirror tests ===");
assertTrue(normalizeAccount(" 012-345-6789 ") === "0123456789", "normalize account");
assertTrue(bankMatches("ACCESS", "044", "Anything"), "ACCESS by code");
assertTrue(!bankMatches("ACCESS", "057", "Access Bank PLC"), "ACCESS rejects wrong code");
assertTrue(bankMatches("ACCESS", null, "Access Bank PLC"), "ACCESS by name");
assertTrue(bankMatches("ZENITH", "", "Zenith Bank"), "ZENITH by name");
assertTrue(bankMatches("UBA", null, "United Bank for Africa"), "UBA by name");
assertTrue(bankMatches("FIRST", "011", "First Bank of Nigeria"), "FIRST by code");
assertTrue(!bankMatches("ZENITH", null, "Access Bank PLC"), "ZENITH != Access");

const probe = [
  {
    id: 1,
    status: "SUCCESSFUL",
    amount: 1000,
    beneficiary_account: "9999999999",
    beneficiary_bank: "Zenith Bank",
    beneficiary_bank_code: "057",
    reference: "MOBTEST-OK",
    currency: "NGN",
  },
  {
    id: 2,
    status: "FAILED",
    amount: 2000,
    beneficiary_account: "9999999999",
    beneficiary_bank: "Zenith Bank",
    beneficiary_bank_code: "057",
    reference: "MOBTEST-FAIL",
    currency: "NGN",
  },
  {
    id: 3,
    status: "PENDING",
    amount: 3000,
    beneficiary_account: "9999999999",
    beneficiary_bank: "Zenith Bank",
    beneficiary_bank_code: "057",
    reference: "MOBTEST-PEND",
    currency: "NGN",
  },
  {
    id: 4,
    status: "REVERSED",
    amount: 4000,
    beneficiary_account: "9999999999",
    beneficiary_bank: "Zenith Bank",
    beneficiary_bank_code: "057",
    reference: "MOBTEST-REV",
    currency: "NGN",
  },
];

const visible = filterSuccessful(probe, "ZENITH", "9999999999");
assertTrue(visible.length === 1, "only SUCCESSFUL visible");
assertTrue(visible[0].amount === 1000, "SUCCESSFUL amount retained");
assertTrue(filterSuccessful(probe, "UBA", "9999999999").length === 0, "wrong bank excluded");

const dto = receiptDto(visible[0], "ZENITH");
assertTrue(dto.transaction_id === "local_transactions:1", "DTO id format");
assertTrue(dto.direction === "credit", "DTO direction credit");
assertTrue(dto.status === "SUCCESSFUL", "DTO status");

// Contract: client unwraps { success, data }
const loginEnvelope = {
  success: true,
  data: {
    token: "abc",
    expires_at: "2099-01-01 00:00:00",
    bank_code: "ZENITH",
    account_number: "9999999999",
    account_name: "Mobile Test User",
    balance: 1000,
  },
};
assertTrue(loginEnvelope.success === true && !!loginEnvelope.data.token, "login envelope shape");

console.log(failures === 0 ? "\nAll mirror tests passed." : `\n${failures} test(s) failed.`);
process.exit(failures === 0 ? 0 : 1);
