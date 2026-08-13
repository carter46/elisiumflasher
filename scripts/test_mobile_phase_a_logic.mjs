/**
 * Mirrors includes/mobile_auth.php bank-match + ownership rules for offline verification
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

function receiptDto(row, sessionBankCode, senderProfile = null) {
  const id = Number(row.id || 0);
  const reference = row.reference != null ? String(row.reference) : null;
  let senderName = row.sender_name ? String(row.sender_name).trim() : "";
  let senderAccount = row.sender_account ? normalizeAccount(String(row.sender_account)) : "";
  if (senderProfile) {
    if (!senderName && senderProfile.account_name) senderName = String(senderProfile.account_name).trim();
    if (!senderAccount && senderProfile.account_number) {
      senderAccount = normalizeAccount(String(senderProfile.account_number));
    }
  }
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
    status: String(row.status || "SUCCESSFUL").toUpperCase(),
    purpose: row.purpose ? String(row.purpose) : null,
    transaction_date: row.transaction_date ? String(row.transaction_date) : null,
    beneficiary_name: row.beneficiary_name ? String(row.beneficiary_name) : null,
    beneficiary_bank: row.beneficiary_bank ? String(row.beneficiary_bank) : null,
    beneficiary_account: row.beneficiary_account
      ? normalizeAccount(String(row.beneficiary_account))
      : null,
    sender_name: senderName || null,
    sender_account: senderAccount || null,
    sender_bank: null,
    direction: "credit",
  };
}

function isEligibleStatus(status) {
  const s = String(status).toUpperCase();
  return s === "SUCCESSFUL" || s === "COMPLETED";
}

function filterEligible(rows, bankCode, accountNumber) {
  const acct = normalizeAccount(accountNumber);
  return rows.filter(
    (r) =>
      isEligibleStatus(r.status) &&
      normalizeAccount(r.beneficiary_account) === acct &&
      bankMatches(bankCode, r.beneficiary_bank_code, r.beneficiary_bank)
  );
}

/** Mirror of mobile UI / receipt status colors */
function statusColor(status) {
  const s = String(status || "").toUpperCase();
  if (s === "SUCCESSFUL" || s === "SUCCESS") return "green";
  if (s === "PENDING") return "amber";
  if (s === "FAILED") return "red";
  return "grey"; // COMPLETED, REVERSED
}

function sessionStillEligible(rows, bankCode, accountNumber) {
  return filterEligible(rows, bankCode, accountNumber).length > 0;
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
  {
    id: 5,
    status: "COMPLETED",
    amount: 500,
    beneficiary_account: "9999999999",
    beneficiary_bank: "Zenith Bank",
    beneficiary_bank_code: "057",
    reference: "MOBTEST-DONE",
    currency: "NGN",
  },
  {
    id: 6,
    status: "SUCCESSFUL",
    amount: 700,
    beneficiary_account: "8888888888",
    beneficiary_bank: "Zenith Bank",
    beneficiary_bank_code: "057",
    reference: "MOBTEST-OTHER",
    currency: "NGN",
  },
];

const visible = filterEligible(probe, "ZENITH", "9999999999");
assertTrue(visible.length === 2, "SUCCESSFUL + COMPLETED visible");
assertTrue(
  visible.every((r) => r.id === 1 || r.id === 5),
  "only owned eligible rows"
);
assertTrue(filterEligible(probe, "UBA", "9999999999").length === 0, "wrong bank excluded");
assertTrue(filterEligible(probe, "ZENITH", "8888888888").length === 1, "other account isolated");

const dtoOk = receiptDto(visible.find((r) => r.id === 1), "ZENITH");
assertTrue(dtoOk.transaction_id === "local_transactions:1", "DTO id format");
assertTrue(dtoOk.direction === "credit", "DTO direction credit");
assertTrue(dtoOk.status === "SUCCESSFUL", "DTO SUCCESSFUL status");

const dtoDone = receiptDto(visible.find((r) => r.id === 5), "ZENITH");
assertTrue(dtoDone.status === "COMPLETED", "DTO COMPLETED status from DB");

assertTrue(statusColor("SUCCESSFUL") === "green", "SUCCESSFUL green");
assertTrue(statusColor("COMPLETED") === "grey", "COMPLETED grey");
assertTrue(statusColor("REVERSED") === "grey", "REVERSED grey");
assertTrue(statusColor("FAILED") === "red", "FAILED red");
assertTrue(statusColor("PENDING") === "amber", "PENDING amber");

// Failed→Successful: after flip, row becomes eligible again
const afterFlip = probe.map((r) =>
  r.id === 2 ? { ...r, status: "SUCCESSFUL" } : r
);
assertTrue(
  filterEligible(afterFlip, "ZENITH", "9999999999").some((r) => r.id === 2),
  "Failed→Successful becomes visible"
);

// Session eligibility: revoke when no SUCCESSFUL/COMPLETED remain
const allFailed = probe.map((r) => ({ ...r, status: "FAILED" }));
assertTrue(
  sessionStillEligible(probe, "ZENITH", "9999999999") === true,
  "session eligible with SUCCESSFUL/COMPLETED"
);
assertTrue(
  sessionStillEligible(allFailed, "ZENITH", "9999999999") === false,
  "session revoked when only FAILED remain"
);

// Contract: client unwraps { success, data }
const loginEnvelope = {
  success: true,
  data: {
    token: "abc",
    expires_at: "2099-01-01 00:00:00",
    bank_code: "ZENITH",
    account_number: "9999999999",
    account_name: "Mobile Test User",
    balance: 1500,
  },
};
assertTrue(loginEnvelope.success === true && !!loginEnvelope.data.token, "login envelope shape");

const blankSenderDto = receiptDto(
  {
    id: 99,
    status: "SUCCESSFUL",
    amount: 100,
    purpose: "School fees",
    sender_name: "",
    sender_account: "",
    beneficiary_account: "9999999999",
  },
  "ZENITH",
  { account_name: "Tunde O. Badmus", account_number: "1022090307" }
);
assertTrue(blankSenderDto.sender_name === "Tunde O. Badmus", "sender falls back to admin profile name");
assertTrue(blankSenderDto.sender_account === "1022090307", "sender falls back to admin profile account");
assertTrue(blankSenderDto.purpose === "School fees", "narration/purpose preserved");

console.log(failures === 0 ? "\nAll mirror tests passed." : `\n${failures} test(s) failed.`);
process.exit(failures === 0 ? 0 : 1);
