<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Linux_Cheat_Sheet extends TextCraft_Tool_Base {
    public function get_name(): string { return 'linux_cheat_sheet'; }
    public function get_title(): string { return 'Linux Commands Cheat Sheet'; }
    public function get_icon(): string { return 'eicon-terminal'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Quick reference for essential Linux commands. Searchable and copy-ready for daily use.</div>

        <div class="tc-input-group" style="margin-bottom:20px">
            <input type="text" class="tc-input" id="linux-search" placeholder="Search commands... (e.g. chmod, grep, find)">
        </div>

        <div class="tctp-result" id="linux-result" style="display:block">
            <div id="linux-content">
                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#2563eb;margin:20px 0 10px;font-size:16px">Navigation & Files</h3>
                    <pre class="tctp-code-block"><code># Navigation
pwd                         # print working directory
ls                          # list files
ls -la                      # list all (including hidden) with details
cd /path/to/dir             # change directory
cd ~                        # go to home directory
cd -                        # go to previous directory

# File operations
cp file.txt backup.txt      # copy file
cp -r dir1/ dir2/           # copy directory recursively
mv old.txt new.txt          # rename / move
rm file.txt                 # delete file
rm -rf dir/                 # delete directory (careful!)
touch file.txt              # create empty file / update timestamp
mkdir -p path/to/dir        # create directories recursively

# View files
cat file.txt                # print entire file
less file.txt               # view with pagination
head -20 file.txt           # first 20 lines
tail -20 file.txt           # last 20 lines
tail -f /var/log/syslog     # follow file in real time</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#2563eb;margin:20px 0 10px;font-size:16px">Search & Find</h3>
                    <pre class="tctp-code-block"><code># Find files
find / -name "*.log" -type f           # find by name
find . -size +100M                     # find large files
find . -mtime -7                       # modified in last 7 days

# Search in files
grep "pattern" file.txt                # search in file
grep -r "pattern" /path/               # recursive search
grep -i "pattern" file.txt             # case-insensitive
grep -n "pattern" file.txt             # show line numbers

# Locate (faster, uses index)
locate filename

# Which / Where
which python                           # find executable path
whereis nginx                          # find binary, source, manual</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#2563eb;margin:20px 0 10px;font-size:16px">Permissions & Ownership</h3>
                    <pre class="tctp-code-block"><code># View permissions
ls -la
stat file.txt

# Change permissions
chmod 755 file.sh          # rwxr-xr-x
chmod 644 file.txt         # rw-r--r--
chmod +x script.sh         # make executable
chmod -R 755 directory/    # recursive

# Change ownership
chown user:group file.txt
chown -R www-data:www-data /var/www/</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#2563eb;margin:20px 0 10px;font-size:16px">Process Management</h3>
                    <pre class="tctp-code-block"><code># List processes
ps aux                      # all processes
ps aux | grep nginx         # filter by name
top                         # real-time process monitor
htop                        # better top (if installed)

# Kill processes
kill PID                    # send SIGTERM
kill -9 PID                 # force kill (SIGKILL)
killall nginx               # kill by name

# Background / Foreground
command &                   # run in background
jobs                        # list background jobs
fg %1                       # bring job to foreground
bg %1                       # resume in background

# System
uptime                      # system uptime
free -h                     # memory usage
df -h                       # disk usage
du -sh /path/               # directory size</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#2563eb;margin:20px 0 10px;font-size:16px">Networking</h3>
                    <pre class="tctp-code-block"><code># Network info
ip addr                     # show IP addresses
ip route                    # show routing table
netstat -tlnp               # listening ports
ss -tlnp                    # listening ports (newer)

# Connect
ping google.com             # test connectivity
curl https://example.com    # HTTP request
wget https://example.com/file.zip  # download file

# SSH
ssh user@hostname           # connect to remote
ssh -p 2222 user@host       # custom port

# SCP (copy over SSH)
scp file.txt user@host:/path/     # upload
scp user@host:/path/file.txt ./   # download

# DNS
dig example.com             # DNS lookup
nslookup example.com</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#2563eb;margin:20px 0 10px;font-size:16px">Disk & Archive</h3>
                    <pre class="tctp-code-block"><code># Disk usage
df -h                       # filesystem usage
du -sh /path/               # directory size
du -sh * | sort -rh         # largest items

# Archive / Compress
tar -czf archive.tar.gz dir/     # create gzip
tar -xzf archive.tar.gz         # extract gzip
tar -cjf archive.tar.bz2 dir/   # create bzip2
tar -xjf archive.tar.bz2        # extract bzip2
zip -r archive.zip dir/          # create zip
unzip archive.zip                # extract zip</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#2563eb;margin:20px 0 10px;font-size:16px">Text Processing</h3>
                    <pre class="tctp-code-block"><code># Sort / Unique
sort file.txt               # sort lines
sort -u file.txt            # sort + remove duplicates
uniq -c                     # count duplicates

# Cut / Awk / Sed
cut -d',' -f1,3 file.csv    # extract columns 1 and 3
awk '{print $1, $3}' file   # print columns 1 and 3
sed 's/old/new/g' file.txt  # replace text

# Count
wc -l file.txt              # count lines
wc -w file.txt              # count words

# Pipe & Redirect
command1 | command2         # pipe output to next command
command > file.txt          # redirect to file (overwrite)
command >> file.txt         # append to file
command 2>&1                # redirect stderr to stdout</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#2563eb;margin:20px 0 10px;font-size:16px">System & Service</h3>
                    <pre class="tctp-code-block"><code># System info
uname -a                    # kernel info
cat /etc/os-release         # OS info
lscpu                       # CPU info

# Service management (systemd)
systemctl start nginx       # start service
systemctl stop nginx        # stop service
systemctl restart nginx     # restart
systemctl status nginx      # check status
systemctl enable nginx      # auto-start on boot

# Cron jobs
crontab -e                  # edit cron jobs
crontab -l                  # list cron jobs
# Format: min hour day month weekday command
# 0 2 * * * /path/to/script.sh  # run daily at 2am</code></pre>
                </div>
            </div>
        </div>
    <?php }

    protected function render_result_content(array $settings): void { ?>
        <div></div>
    <?php }
}
