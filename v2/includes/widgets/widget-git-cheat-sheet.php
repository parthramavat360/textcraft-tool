<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Git_Cheat_Sheet extends TextCraft_Tool_Base {
    public function get_name(): string { return 'git_cheat_sheet'; }
    public function get_title(): string { return 'Git Cheat Sheet'; }
    public function get_icon(): string { return 'eicon-code-bold'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Quick reference for Git commands including branching, merging, rebasing, stashing, and more. Searchable and copy-ready.</div>

        <div class="tc-input-group" style="margin-bottom:20px">
            <input type="text" class="tc-input" id="git-search" placeholder="Search commands... (e.g. branch, merge, stash)">
        </div>

        <div class="tctp-result" id="git-result" style="display:block">
            <div id="git-content">
                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Setup & Config</h3>
                    <pre class="tctp-code-block"><code># Set your name and email
git config --global user.name "Your Name"
git config --global user.email "you@example.com"

# Set default editor
git config --global core.editor "code --wait"

# View all config
git config --list

# Create new repository
git init

# Clone a repository
git clone https://github.com/user/repo.git
git clone --depth 1 https://github.com/user/repo.git  # shallow clone</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Staging & Committing</h3>
                    <pre class="tctp-code-block"><code># Check status
git status

# Stage files
git add file.txt            # stage single file
git add .                   # stage all changes
git add -p                  # stage interactively (patch mode)

# Commit
git commit -m "message"     # commit staged changes
git commit -am "message"    # stage all tracked files + commit
git commit --amend          # amend last commit

# Unstage
git reset HEAD file.txt     # unstage a file
git restore --staged file.txt  # unstage (newer syntax)</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Branching</h3>
                    <pre class="tctp-code-block"><code># List branches
git branch                  # local branches
git branch -a               # all branches (local + remote)
git branch -v               # last commit on each branch

# Create & switch
git branch feature-name     # create branch
git checkout feature-name   # switch to branch
git checkout -b feature-name  # create + switch
git switch feature-name     # switch (newer syntax)
git switch -c feature-name  # create + switch (newer syntax)

# Delete branch
git branch -d feature-name  # delete (safe)
git branch -D feature-name  # force delete

# Rename
git branch -m old-name new-name</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Merging & Rebasing</h3>
                    <pre class="tctp-code-block"><code># Merge
git checkout main
git merge feature-branch    # merge feature into main
git merge --no-ff feature   # merge with merge commit (no fast-forward)

# Rebase
git checkout feature
git rebase main             # replay feature commits on top of main

# Interactive rebase (edit last 3 commits)
git rebase -i HEAD~3

# Abort rebase
git rebase --abort

# Continue rebase (after resolving conflicts)
git rebase --continue

# Cherry-pick
git cherry-pick abc1234     # apply commit abc1234</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Stashing</h3>
                    <pre class="tctp-code-block"><code># Stash changes
git stash                   # stash all changes
git stash push -m "message"  # stash with message
git stash -u                # stash including untracked files

# List stashes
git stash list

# Apply stash
git stash apply             # apply without removing
git stash pop               # apply + remove
git stash apply stash@{2}   # apply specific stash

# Drop stash
git stash drop              # drop latest
git stash drop stash@{0}    # drop specific

# Clear all stashes
git stash clear</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Remote & Push/Pull</h3>
                    <pre class="tctp-code-block"><code># Remote management
git remote add origin https://github.com/user/repo.git
git remote -v               # list remotes
git remote remove origin    # remove remote

# Push
git push origin main        # push main to origin
git push -u origin feature  # push + set upstream
git push --force-with-lease # safe force push

# Pull
git pull origin main        # fetch + merge
git pull --rebase origin main  # fetch + rebase

# Fetch
git fetch                   # fetch all remotes
git fetch origin            # fetch specific remote</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Viewing History</h3>
                    <pre class="tctp-code-block"><code># Log
git log                     # full log
git log --oneline           # compact log
git log --oneline -10       # last 10 commits
git log --graph --oneline   # graph view
git log --author="John"     # filter by author
git log --since="2024-01-01" # filter by date

# Show specific commit
git show abc1234

# Diff
git diff                    # unstaged changes
git diff --staged           # staged changes
git diff main..feature      # diff between branches
git diff abc1234 def5678    # diff between commits

# Blame
git blame file.txt          # who changed each line</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Undoing Changes</h3>
                    <pre class="tctp-code-block"><code># Unstaged changes
git restore file.txt        # discard changes

# Staged changes
git restore --staged file.txt  # unstage

# Amend last commit
git commit --amend -m "new message"

# Revert commit (creates new commit)
git revert abc1234

# Reset (DANGEROUS - rewrites history)
git reset --soft HEAD~1     # undo commit, keep changes staged
git reset --mixed HEAD~1    # undo commit, keep changes unstaged
git reset --hard HEAD~1     # undo commit, discard changes

# Reflog (recover lost commits)
git reflog
git checkout abc1234</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Tags</h3>
                    <pre class="tctp-code-block"><code># List tags
git tag

# Create tag
git tag v1.0.0              # lightweight tag
git tag -a v1.0.0 -m "Release 1.0"  # annotated tag

# Push tags
git push origin v1.0.0      # push single tag
git push --tags              # push all tags

# Delete tag
git tag -d v1.0.0            # delete local
git push origin --delete v1.0.0  # delete remote</code></pre>
                </div>
            </div>
        </div>
    <?php }

    protected function render_result_content(array $settings): void { ?>
        <div></div>
    <?php }
}
