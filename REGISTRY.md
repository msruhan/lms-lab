# Lab Images Registry

Lab container images are published to **GitHub Container Registry (GHCR)** under the same GitHub user that owns this repo.

## Image catalog

| Logical name        | Image                                                          | Default tag | Source folder        |
| ------------------- | -------------------------------------------------------------- | ----------- | -------------------- |
| THM Lite Desktop    | `ghcr.io/<owner>/lms-thm-lite-desktop`                         | `v1`        | `thm-lite-desktop/`  |
| CTF SQLi Lab        | `ghcr.io/<owner>/lms-ctf-sqli-lab`                             | `v2`        | `ctf-sqli-lab/`      |

`<owner>` is the GitHub username/org that owns this repository. For msruhan that resolves to:

- `ghcr.io/msruhan/lms-thm-lite-desktop:v1`
- `ghcr.io/msruhan/lms-ctf-sqli-lab:v2`

Each successful build also pushes:

- `latest` (always points to the newest main-branch build).
- `sha-<short>` (immutable, useful for debugging or pinning).

## How builds happen

The workflow `.github/workflows/build-lab-images.yml` runs:

- automatically on every push to `main` that touches one of the image folders (or the workflow file itself), and
- manually via "Run workflow" in the GitHub Actions tab (you can pick one image or all).

Builds use Buildx with GHA cache so subsequent runs are fast. The workflow uses `secrets.GITHUB_TOKEN` so no extra credentials are needed.

## Make published images public (recommended for solo dev)

By default GHCR images are private. Set them public once so the VPS can pull without a login:

1. Open https://github.com/users/<owner>/packages
2. Pick `lms-thm-lite-desktop` (and `lms-ctf-sqli-lab`).
3. Settings → Change visibility → Public.

If you prefer to keep them private, the VPS needs to log in once:

```bash
echo "$GHCR_TOKEN" | docker login ghcr.io -u <owner> --password-stdin
```

`GHCR_TOKEN` is a GitHub Personal Access Token with `read:packages` scope.

## Pull on the host (sanity check)

```bash
docker pull ghcr.io/<owner>/lms-thm-lite-desktop:v1
docker pull ghcr.io/<owner>/lms-ctf-sqli-lab:v2
docker images | grep ghcr
```

## Backend integration

Backend (`LMS-Backend/app/Services/LabContainerService.php`) reads the attacker
image map from this registry naming convention. Target images are picked from
the `lab_machines` table; `LabMachineSeeder` already references the GHCR tags
above. Run `php artisan db:seed --class=LabMachineSeeder --force` after pulling
the latest backend so the DB matches.

## Manual build (developer machine)

If you need to build locally without GHCR:

```bash
# attacker (kasm-based desktop)
docker build -t lms/thm-lite-desktop:v1 ./thm-lite-desktop

# CTF SQLi target
docker build -t lms/ctf-sqli-lab:v2 ./ctf-sqli-lab
```

These image names also work because the seeder/service falls back to plain
`lms/...` tags if you keep them in sync.

## Versioning policy

- Bump the major suffix (`v1 → v2`) when behaviour changes incompatibly.
- Use `sha-<short>` for production pins when you need to rollback fast.
- Never overwrite a published `vN` tag with breaking content; cut a new
  major instead.
