#!/bin/bash

# ========================================================
# KONFIGURASI WARNA TAMPILAN TERMUX
# ========================================================
HIJAU='\033[1;32m'
KUNING='\033[1;33m'
BIRU='\033[1;36m'
UNGU='\033[1;35m'
MERAH='\033[1;31m'
PUTIH='\033[1;37m'
ABU='\033[0;37m'
RESET='\033[0m'

# File konfigurasi lokal untuk menyimpan settingan user
CONFIG_FILE=".git_manager_config"

# Load konfigurasi jika sudah ada, atau gunakan default
if [ -f "$CONFIG_FILE" ]; then
    source "$CONFIG_FILE"
else
    REPO_URL="https://github.com/Hklrky/Absensi-QR.git"
    BRANCH_UTAMA="main"
    GIT_USER_NAME="Hklrky"
    GIT_USER_EMAIL="hklrky@users.noreply.github.com"
fi

# Simpan fungsi simpan konfigurasi
simpan_config() {
    cat <<EOF > "$CONFIG_FILE"
REPO_URL="$REPO_URL"
BRANCH_UTAMA="$BRANCH_UTAMA"
GIT_USER_NAME="$GIT_USER_NAME"
GIT_USER_EMAIL="$GIT_USER_EMAIL"
EOF
}

# ========================================================
# INISIALISASI & PERBAIKAN OTOMATIS TERMUX
# ========================================================
git config --global --add safe.directory "$(pwd)" 2>/dev/null
git config --global credential.helper store
git config --global user.name "$GIT_USER_NAME"
git config --global user.email "$GIT_USER_EMAIL"

# Pastikan folder sudah menjadi repositori Git
if [ ! -d ".git" ]; then
    git init -q
    git branch -M "$BRANCH_UTAMA" 2>/dev/null
    git remote add origin "$REPO_URL" 2>/dev/null
fi

if [ -z "$(git remote get-url origin 2>/dev/null)" ]; then
    git remote add origin "$REPO_URL"
fi

# Fungsi jeda layar
jeda() {
    echo -e "\n${KUNING}Tekan [ENTER] untuk kembali ke menu utama...${RESET}"
    read -r
}

# ========================================================
# LOOP MENU UTAMA INTERAKTIF
# ========================================================
while true; do
    clear
    JUMLAH_UBAH=$(git status -s | wc -l)
    BRANCH_AKTIF=$(git branch --show-current 2>/dev/null || echo "$BRANCH_UTAMA")

    echo -e "${KUNING}========================================================${RESET}"
    echo -e "${BIRU}     ⚡ GIT MANAGER PRO - ABSENSI QR COMIC ⚡          ${RESET}"
    echo -e "${KUNING}========================================================${RESET}"
    echo -e "${ABU}Folder : ${PUTIH}$(basename "$(pwd)")${RESET}  |  ${ABU}Branch : ${HIJAU}$BRANCH_AKTIF${RESET}"
    echo -e "${ABU}Status : ${KUNING}$JUMLAH_UBAH file belum di-push${RESET}"
    echo -e "${KUNING}--------------------------------------------------------${RESET}"
    echo -e "${HIJAU}[1]${RESET} 📋 Cek Status & Daftar File Berubah"
    echo -e "${HIJAU}[2]${RESET} 🚀 Push Normal ke GitHub (Otomatis & Aman)"
    echo -e "${KUNING}[3]${RESET} ⚡ Force Push (Paksa Timpa jika Bentrok/Rejected)"
    echo -e "${BIRU}[4]${RESET} ⬇️  Tarik Update dari GitHub (Git Pull)"
    echo -e "${UNGU}[5]${RESET} ⚙️  Menu Konfigurasi (Ubah URL/Branch/User)"
    echo -e "${MERAH}[6]${RESET} 🧹 Bersihkan/Reset Total Git (Mulai Push dari Nol)"
    echo -e "${PUTIH}[7]${RESET} 📜 Lihat Riwayat 5 Commit Terakhir"
    echo -e "${MERAH}[8]${RESET} 🔑 Hapus/Reset Simpanan Token GitHub"
    echo -e "${ABU}[0]${RESET} ❌ Keluar"
    echo -e "${KUNING}--------------------------------------------------------${RESET}"
    read -p "Pilih menu [0-8]: " PILIHAN

    case $PILIHAN in
        1)
            echo -e "\n${BIRU}=== 📋 STATUS PERUBAHAN FILE ===${RESET}"
            if [[ -z $(git status -s) ]]; then
                echo -e "${HIJAU}✨ Bersih! Semua file lokal sudah sama persis dengan GitHub.${RESET}"
            else
                git status -s
                echo -e "\n${ABU}Keterangan: [M] = Diubah, [??] = File Baru, [D] = Dihapus${RESET}"
            fi
            jeda
            ;;

        2)
            echo -e "\n${BIRU}=== 🚀 SMART PUSH KE GITHUB ===${RESET}"
            if [[ -z $(git status -s) ]]; then
                echo -e "${MERAH}[!] GAGAL: Tidak ada perubahan file baru untuk di-push!${RESET}"
                jeda
                continue
            fi

            echo -e "${PUTIH}File yang akan dikirim:${RESET}"
            git status -s
            echo ""
            read -p "Lanjutkan kirim file di atas? (y/n): " KONFIRMASI
            if [[ "$KONFIRMASI" != "y" && "$KONFIRMASI" != "Y" ]]; then
                echo -e "${KUNING}Proses push dibatalkan.${RESET}"
                jeda
                continue
            fi

            git add .
            read -p "Tulis pesan commit (ENTER untuk otomatis): " PESAN_COMMIT
            if [ -z "$PESAN_COMMIT" ]; then
                WAKTU=$(date +"%d-%m-%Y %H:%M")
                PESAN_COMMIT="Update Absensi QR ($WAKTU)"
            fi

            git commit -m "$PESAN_COMMIT"
            echo -e "\n${PUTIH}Sedang mengunggah ke GitHub...${RESET}"
            
            if git push -u origin "$BRANCH_UTAMA"; then
                echo -e "\n${HIJAU}🎉 BERHASIL! Semua perubahan sudah tampil di GitHub!${RESET}"
            else
                echo -e "\n${MERAH}⛔ PUSH DITOLAK! Coba gunakan Menu [3] (Force Push) atau Menu [6].${RESET}"
            fi
            jeda
            ;;

        3)
            echo -e "\n${KUNING}=== ⚡ FORCE PUSH (PAKSA TIMPA GITHUB) ===${RESET}"
            echo -e "${MERAH}Peringatan: Ini akan memaksa isi folder HP menimpa total riwayat di GitHub.${RESET}"
            read -p "Yakin ingin Force Push? (y/n): " YAKIN_FORCE
            if [[ "$YAKIN_FORCE" == "y" || "$YAKIN_FORCE" == "Y" ]]; then
                git add .
                git commit -m "Force update Absensi QR" 2>/dev/null
                if git push -u origin "$BRANCH_UTAMA" --force; then
                    echo -e "\n${HIJAU}🎉 FORCE PUSH BERHASIL!${RESET}"
                else
                    echo -e "\n${MERAH}⛔ Gagal Force Push.${RESET}"
                fi
            fi
            jeda
            ;;

        4)
            echo -e "\n${BIRU}=== ⬇️ TARIK UPDATE DARI GITHUB (PULL) ===${RESET}"
            if git pull origin "$BRANCH_UTAMA" --allow-unrelated-histories; then
                echo -e "\n${HIJAU}✨ Berhasil menyinkronkan file dari GitHub!${RESET}"
            else
                echo -e "\n${MERAH}⛔ Terjadi konflik saat menarik data.${RESET}"
            fi
            jeda
            ;;

        5)
            echo -e "\n${UNGU}=== ⚙️ MENU KONFIGURASI REPOSITORI ===${RESET}"
            echo -e "URL Repo Saat Ini  : ${PUTIH}$REPO_URL${RESET}"
            echo -e "Branch Saat Ini    : ${PUTIH}$BRANCH_UTAMA${RESET}"
            echo -e "Git Username       : ${PUTIH}$GIT_USER_NAME${RESET}"
            echo -e "----------------------------------------"
            echo -e "1. Ubah URL Repositori GitHub"
            echo -e "2. Ubah Nama Branch Utama (Contoh: main / master)"
            echo -e "3. Ubah Nama & Email Git"
            echo -e "0. Kembali"
            read -p "Pilih opsi [0-3]: " OPSI_CFG

            if [ "$OPSI_CFG" == "1" ]; then
                read -p "Masukkan URL Repository baru: " BARU_URL
                if [ ! -z "$BARU_URL" ]; then
                    REPO_URL="$BARU_URL"
                    git remote set-url origin "$REPO_URL" 2>/dev/null || git remote add origin "$REPO_URL"
                    simpan_config
                    echo -e "${HIJAU}[✓] URL Repository berhasil diubah!${RESET}"
                fi
            elif [ "$OPSI_CFG" == "2" ]; then
                read -p "Masukkan nama branch baru (misal: main atau master): " BARU_BRANCH
                if [ ! -z "$BARU_BRANCH" ]; then
                    BRANCH_UTAMA="$BARU_BRANCH"
                    git branch -M "$BRANCH_UTAMA"
                    simpan_config
                    echo -e "${HIJAU}[✓] Branch utama berhasil diubah ke '$BRANCH_UTAMA'!${RESET}"
                fi
            elif [ "$OPSI_CFG" == "3" ]; then
                read -p "Masukkan Username Git baru: " BARU_USER
                read -p "Masukkan Email Git baru: " BARU_EMAIL
                if [ ! -z "$BARU_USER" ] && [ ! -z "$BARU_EMAIL" ]; then
                    GIT_USER_NAME="$BARU_USER"
                    GIT_USER_EMAIL="$BARU_EMAIL"
                    git config --global user.name "$GIT_USER_NAME"
                    git config --global user.email "$GIT_USER_EMAIL"
                    simpan_config
                    echo -e "${HIJAU}[✓] Identitas Git berhasil diperbarui!${RESET}"
                fi
            fi
            jeda
            ;;

        6)
            echo -e "\n${MERAH}=== 🧹 BERSIHKAN / RESET TOTAL GIT ===${RESET}"
            echo -e "${KUNING}Fitur ini akan menghapus seluruh riwayat commit sebelumnya dan membuat sejarah baru.${RESET}"
            echo -e "${KUNING}File kodinganmu aman (tidak akan terhapus), hanya riwayat Git-nya saja yang di-reset.${RESET}"
            read -p "Yakin ingin reset total Git? (y/n): " YAKIN_RESET
            if [[ "$YAKIN_RESET" == "y" || "$YAKIN_RESET" == "Y" ]]; then
                rm -rf .git
                git init -q
                git branch -M "$BRANCH_UTAMA"
                git remote add origin "$REPO_URL"
                git add .
                git commit -m "Initial commit (Reset Total)"
                echo -e "\n${PUTIH}Melakukan Force Push pertama kali setelah reset...${RESET}"
                if git push -u origin "$BRANCH_UTAMA" --force; then
                    echo -e "\n${HIJAU}🎉 RESET BERHASIL! Riwayat Git dibersihkan dan kode berhasil di-push ulang dari nol!${RESET}"
                else
                    echo -e "\n${MERAH}⛔ Gagal melakukan push setelah reset. Periksa token atau koneksi internet.${RESET}"
                fi
            else
                echo -e "${ABU}Reset dibatalkan.${RESET}"
            fi
            jeda
            ;;

        7)
            echo -e "\n${BIRU}=== 📜 5 RIWAYAT COMMIT TERAKHIR ===${RESET}"
            git log -n 5 --stat --oneline
            jeda
            ;;

        8)
            echo -e "\n${MERAH}=== 🔑 RESET KREDENSIAL / TOKEN GITHUB ===${RESET}"
            rm -f ~/.git-credentials
            git config --global credential.helper store
            echo -e "${HIJAU}[✓] Simpanan token lama telah dihapus.${RESET}"
            jeda
            ;;

        0)
            echo -e "\n${HIJAU}Keluar dari program. Semangat ngodingnya! 🚀${RESET}"
            exit 0
            ;;

        *)
            echo -e "\n${MERAH}Pilihan tidak valid!${RESET}"
            sleep 1
            ;;
    esac
done