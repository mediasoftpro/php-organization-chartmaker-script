Do you have a contribution? We welcome contributions, but please ensure that you read the following information before issuing a pull request.

## Before Starting

#### Understanding the Basics
If you don't understand what a pull request is, or how to submit one, please refer to the [help documentation](https://help.github.com/articles/about-pull-requests/ "help documentation") provided by GitHub

#### Is It Really a Support Issue

If you aren't sure if your contribution is needed or necessary, please visit the support forum before attempting to submit a pull request or a ticket.

#### Search the Project Issues
It is useful to make sure that your issue isn't already known or otherwise addressed by checking the issues list for this project. If you think it is a valid defect or enhancement, feel free to submit your pull request.

#### Discuss Non-Trivial Contributions with the Committers
If your desired contribution is more than a non-trivial fix, you should discuss it on the contributor's mailing list. If you currently are not a member, you can request to be added.

#### Contributor License Agreement
We require all contributions to be covered under MIT License.

## Submitting a Pull Request

The following are the general steps you should follow in creating a pull request. Subsequent pull requests only need to follow step 3 and beyond:
- Fork the repository on GitHub
- Clone the forked repository to your machine
- Create a "feature" branch in your local repository
- Make your changes and commit them to your local repository
- Rebase and push your commits to your GitHub remote fork/repository
- Issue a Pull Request to the official repository
- Your Pull Request is reviewed by a committer and merged into the repository

### 1. Fork the Repository
When logged in to your GitHub account, and you are viewing one of the main repositories, you will see the Fork button. Clicking this button will show you which repositories you can fork to. Choose your own account. Once the process finishes, you will have your own repository that is "forked" from the official one.

Forking is a GitHub term and not a git term. Git is a wholly distributed source control system and simply worries about local and remote repositories and allows you to manage your code against them. GitHub then adds this additional layer of structure of how repositories can relate to each other.

### 2. Clone the Forked Repository
Once you have successfully forked your repository, you will need to clone it locally to your machine:

```bash
$ git clone --recursive git@github.com:mediasoftpro/php-organization-chartmaker-script.git php-organization-chartmaker-script
```

This will clone your fork to your current path in a directory named php-organization-chartmaker-script.

You should also set up the upstream repository. This will allow you to take changes from the "master" repository and merge them into your local clone and then push them to your GitHub fork:

    $ cd php-organization-chartmaker-script
    $ git remote add upstream git@github.com/mediasoftpro/php-organization-chartmaker-script.git
    $ git fetch upstream


Then you can retrieve upstream changes and rebase on them into your code like this:

```bash
$ git pull --rebase upstream master
```

### 3. Create a Branch
The easiest workflow is to keep your master branch in sync with the upstream branch and do not locate any of your own commits in that branch. When you want to work on a new feature, you then ensure you are on the master branch and create a new branch from there. While the name of the branch can be anything, it can often be easy to use the issue number you might be working on (if an issue was opened prior to opening a pull request). For example:

```bash
$ git checkout -b issue-12345 master
Switched to a new branch 'issue-12345'
```
You will then be on the feature branch. You can verify what branch you are on like this:

```bash
$ git status
# On branch issue-12345
nothing to commit, working directory clean
```
### 4. Make Changes and Commit

Now you just need to make your changes. Once you have finished your changes (and tested them) you need to commit them to your local repository (assuming you have staged your changes for committing):

```bash
$ git status
# On branch issue-12345
# Changes to be committed:
#   (use "git reset HEAD <file>..." to unstage)
#
#        modified:   somefile.js
#
$ git commit -m "Corrects some defect, fixes #12345, refs #12346"
[t12345 0000000] Corrects some defect, fixes #12345, refs #12346
 1 file changed, 2 insertions(+), 2 deletions(-)
```

### 5. Rebase and Push Changes
If you have been working on your contribution for a while, the upstream repository may have changed. You may want to ensure your work is on top of the latest changes so your pull request can be applied cleanly:

```bash
$ git pull --rebase upstream master
```
When you are ready to push your commit to your GitHub repository for the first time on this branch you would do the following:

```bash
$ git push -u origin issue-12345
```
After the first time, you simply need to do:

$ git push

### 6. Issue a Pull Request

In order to have your commits merged into the main repository, you need to create a pull request. The instructions for this can be found in the [GitHub Help Article](https://help.github.com/articles/creating-a-pull-request "GitHub Help Article") Creating a Pull Request. Essentially you do the following:

- Go to the site for your repository.
- Click the Pull Request button.
- Select the feature branch from your repository.
- Enter a title and description of your pull request in the description.
- Review the commit and files changed tabs.
- Click Send Pull Request

You will get notified about the status of your pull request based on your GitHub settings.

### 7. Request is Reviewed and Merged
Your request will be reviewed. It may be merged directly, or you may receive feedback or questions on your pull request.