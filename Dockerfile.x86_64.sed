/### ARM BEGIN/,/### ARM END/s/^[# ]*(.*)/# \1/g
s/[# ]*(RUN [^a-z]*cross-build-start[^a-z]*)/# \1/g
s/[# ]*(RUN [^a-z]*cross-build-end[^a-z]*)/# \1/g
